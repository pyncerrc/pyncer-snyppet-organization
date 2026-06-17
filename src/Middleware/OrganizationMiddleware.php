<?php
namespace Pyncer\Snyppet\Organization\Middleware;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Access\AuthenticatorInterface;
use Pyncer\Access\BearerAuthenticatorInterface;
use Pyncer\Data\MapperQuery\FiltersQueryParam;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Exception\UnexpectedValueException;
use Pyncer\Http\Server\MiddlewareInterface;
use Pyncer\Http\Server\RequestHandlerInterface;
use Pyncer\Snyppet\Access\Table\Token\TokenModel;
use Pyncer\Snyppet\Organization\Initializer;
use Pyncer\Snyppet\Organization\Table\Organization\OrganizationMapper;
use Pyncer\Snyppet\Organization\Table\Organization\OrganizationMapperQuery;
use Pyncer\Snyppet\Organization\Table\Organization\OrganizationModel;
use Pyncer\Snyppet\Organization\Table\Organization\DataManager as UserDataManager;
use Pyncer\Snyppet\Organization\Table\Organization\User\UserMapper as OrganizationUserMapper;
use Pyncer\Snyppet\Organization\Table\Organization\User\UserModel as OrganizationUserModel;
use Pyncer\Snyppet\Organization\Table\Organization\ValueManager as UserValueManager;
use Pyncer\Snyppet\Role\RoleManager;

use const Pyncer\Snyppet\Organization\INITIALIZER as PYNCER_ORGANIZATION_INITIALIZER;
use const Pyncer\Snyppet\Organization\INITIALIZER_TOKEN_SCHEME as PYNCER_ORGANIZATION_INITIALIZER_TOKEN_SCHEME;
use const Pyncer\Snyppet\Organization\INITIALIZER_TOKEN_REALM as PYNCER_ORGANIZATION_INITIALIZER_TOKEN_REALM;
use const Pyncer\Snyppet\Organization\INITIALIZER_HEADER_ID as PYNCER_ORGANIZATION_INITIALIZER_HEADER_ID;
use const Pyncer\Snyppet\Organization\INITIALIZER_HEADER_UID as PYNCER_ORGANIZATION_INITIALIZER_HEADER_UID;
use const Pyncer\Snyppet\Organization\INITIALIZER_HEADER_ALIAS as PYNCER_ORGANIZATION_INITIALIZER_HEADER_ALIAS;
use const Pyncer\Snyppet\Organization\INITIALIZER_QUERY_PARAM_ID as PYNCER_ORGANIZATION_INITIALIZER_QUERY_PARAM_ID;
use const Pyncer\Snyppet\Organization\INITIALIZER_QUERY_PARAM_UID as PYNCER_ORGANIZATION_INITIALIZER_QUERY_PARAM_UID;
use const Pyncer\Snyppet\Organization\INITIALIZER_QUERY_PARAM_ALIAS as PYNCER_ORGANIZATION_INITIALIZER_QUERY_PARAM_ALIAS;

use const Pyncer\Snyppet\Organization\AUTO_INSERT

class OrganizationMiddleware implements MiddlewareInterface
{
    public function __invoke(
        PsrServerRequestInterface $request,
        PsrResponseInterface $response,
        RequestHandlerInterface $handler
    ): PsrResponseInterface
    {
        if (PYNCER_ORGANIZATION_INITIALIZER === null) {
            return $handler->next($request, $response);
        }

        // Database
        if (!$handler->has(ID::DATABASE)) {
            throw new UnexpectedValueException(
                'Database connection expected.'
            );
        }

        $connection = $handler->get(ID::DATABASE);
        if (!$connection instanceof ConnectionInterface) {
            throw new UnexpectedValueException(
                'Invalid database connection.'
            );
        }

        // Access
        if (!$handler->has(ID::ACCESS)) {
            throw new UnexpectedValueException(
                'Access authenticator expected.'
            );
        }

        $access = $handler->get(ID::ACCESS);
        if (!$access instanceof AuthenticatorInterface) {
            throw new UnexpectedValueException('Invalid access authenticator.');
        }

        if ($access->isGuest()) {
            return $handler->next($request, $response);
        }

        $initializer = Initializer::from(PYNCER_ORGANIZATION_INITIALIZER);
        $organizationModel = null;

        switch ($initializer) {
            case Initializer::TOKEN:
                if (!$access instanceof BearerAuthenticatorInterface) {
                    break;
                }

                $organizationModel = $this->getOrganizationModelFromToken(
                    $connection,
                    $access->getUserId(),
                    $access->getToken(),
                );
                break;
            case Initializer::HEADER_ID:
                $id = intval($request->getHeaderLine(PYNCER_ORGANIZATION_INITIALIZER_HEADER_ID));
                $organizationModel = $this->getOrganizationModelFromId(
                    $connection,
                    $access->getUserId(),
                    $id
                );
                break;
            case Initializer::HEADER_UID:
                $uid = $request->getHeaderLine(PYNCER_ORGANIZATION_INITIALIZER_HEADER_UID);
                $organizationModel = $this->getOrganizationModelFromUid(
                    $connection,
                    $access->getUserId(),
                    $uid
                );
                break;
            case Initializer::HEADER_ALIAS:
                $alias = $request->getHeaderLine(PYNCER_ORGANIZATION_INITIALIZER_HEADER_ALIAS);
                $organizationModel = $this->getOrganizationModelFromAlias(
                    $connection,
                    $access->getUserId(),
                    $alias
                );
                break;
            case Initializer::QUERY_PARAM_ID:
                $query = $request->getQueryParams();
                $id = intval($query[PYNCER_ORGANIZATION_INITIALIZER_HEADER_ID] ?? 0);
                $organizationModel = $this->getOrganizationModelFromId(
                    $connection,
                    $access->getUserId(),
                    $id
                );
                break;
            case Initializer::QUERY_PARAM_UID:
                $query = $request->getQueryParams();
                $uid = strval($query[PYNCER_ORGANIZATION_INITIALIZER_HEADER_UID] ?? '');
                $organizationModel = $this->getOrganizationModelFromUid(
                    $connection,
                    $access->getUserId(),
                    $uid
                );
                break;
            case Initializer::QUERY_PARAM_ALIAS:
                $query = $request->getQueryParams();
                $alias = strval($query[PYNCER_ORGANIZATION_INITIALIZER_HEADER_ALIAS] ?? '');
                $organizationModel = $this->getOrganizationModelFromAlias(
                    $connection,
                    $access->getUserId(),
                    $alias
                );
                break;
        }

        if ($organizationModel === null) {
            return $handler->next($request, $response);
        }

        $organizationUserModel = $this->getOrganizationUserModel(
            $connection,
            $organizationModel,
            $userId,
        );

        if ($organizationUserModel === null) {
            return $handler->next($request, $response);
        }

        if ($handler->has(ID::role())) {
            $roleManager = $handler->get(ID::role());
            if ($roleManager instanceof RoleManager) {
                $roles = $this->getRoles(
                    $connection,
                    $organizationUserModel,
                );

                $roleManager->addRoles($roles);
            }
        }

        ID::register(ID::organization());
        ID::register(ID::organization('user'));
        ID::register(ID::organization('data'));
        ID::register(ID::organization('value'));

        $handler->set(ID::organization(), $organizationModel);
        $handler->set(ID::organization('user'), $organizationUserModel);

        $dataManager = new OrganizationDataManager($connection, $organizationModel->getId());
        $handler->set(ID::organization('data'), $dataManager);

        $valueManager = new OrganizationValueManager($connection, $organizationModel->getId());
        $valueManager->preload();
        $handler->set(ID::organization('value'), $valueManager);

        return $handler->next($request, $response);
    }

    protected function getOrganizationModelFromToken(
        ConnectionInterface $connection,
        int $userId,
        TokenModel $tokenModel,
    ): ?OrganizatoinModel
    {
        if ($tokenModel->getScheme() !== PYNCER_ORGANIZATION_INITIALIZER_TOKEN_SCHEME ||
            $tokenModel->getRealm() !== PYNCER_ORGANIZATION_INITIALIZER_TOKEN_REALM
        ) {
            return null;
        }

        $mapper = new OrganizationMapper($connection);

        $mapperQuery = new OrganizationMapperQuery($connection);
        $mapperQuery->setFilters(new FiltersQueryParam(
            'enabled eq true and deleted eq false'
        ));

        return $mapper->selectByTokenId($tokenModel->getId(), $mapperQuery);
    }

    private function getOrganizationModelFromId(
        ConnectionInterface $connection,
        int $organizationId
    ): ?OrganizatoinModel
    {
        if ($organizationId === 0) {
            return null;
        }

        $mapper = new OrganizationMapper($connection);

        $mapperQuery = new OrganizationMapperQuery($connection);
        $mapperQuery->setFilters(new FiltersQueryParam(
            'enabled eq true and deleted eq false'
        ));

        return $mapper->selectById($organizationId, $mapperQuery);
    }

    private function getOrganizationModelFromUid(
        ConnectionInterface $connection,
        string $organizationUid
    ): ?OrganizatoinModel
    {
        $organizationUid = trim($organizationUid);
        if ($organizationUid === '') {
            return null;
        }

        $mapper = new OrganizationMapper($connection);

        $mapperQuery = new OrganizationMapperQuery($connection);
        $mapperQuery->setFilters(new FiltersQueryParam(
            'enabled eq true and deleted eq false'
        ));

        return $mapper->selectByUid($organizationUid, $mapperQuery);
    }

    private function getOrganizationModelFromAlias(
        ConnectionInterface $connection,
        string $organizationAlias,
        int $userId,
    ): ?OrganizatoinModel
    {
        $organizationAlias = trim($organizationAlias);
        if ($organizationAlias === '') {
            return null;
        }

        $row = $connection->select('organization')
            ->join('organization__user', 'organization_id', 'id')
            ->getWhere()
            ->compare('alias', $organizationAlias)
            ->compare('deleted', false)
            ->compare('enabled', true)
            ->compare(['organization__user', 'user_id'], $userId)
            ->compare(['organization__user', 'pending'], false)
            ->compare(['organization__user', 'enabled'], true)
            ->getQuery()
            ->row();

        if ($row === null) {
            return null;
        }

        return new OrganizationModel($row);
    }

    private function getOrganizationUserModel(
        ConnectionInterface $connection,
        OrganizationModel $organizationModel,
        int $userId,
    ): ? OrganizationUserModel
    {
        $mapper = new OrganizationUserMapper($connection);

        $model = $mapper->selectByColumns(
            [
                'organization_id' => $organizationModel->getId(),
                'user_id' => $userId,
            ],
            $mapperQuery
        );

        if ($model !== null) {
            if ($model->getPending() || !$model->getEnabled()) {
                return null;
            }

            return $model;
        }

        if ($organizationModel->getUserId() !== $userId()) {
            return null;
        }

        // Automatically insert organization user for owner
        $userName = $connection->select('user')
            ->columns('name')
            ->where(['id' => $organizationModel->getUserId()])
            ->value();

        $model = new OrganizationUserModel([
            'organizatoin_id' => $organizationModel->getId(),
            'user_id' => $organizationModel->getUserId(),
            'group' => 'super',
            'name' => $userName,
            'enabled' => true,
        ]);

        $mapper->insert($model);

        return $model;
    }

    private function getRoles(
        ConnectionInterface $connection,
        OrganizationUserModel $organizationUserModel,
    ): array
    {
        $query = $this->connection->select('role')
            ->columns('alias')
            ->join('organization__user__role', 'role_id', 'id');

        $where = $query->getWhere();

        $where->compare('enabled', true)
            ->compare('deleted', false)
            ->compare(['organization__user__role', 'user_id'], $organizationUserModel->getUserId());

        switch ($organizationUserModel->getGroup()) {
            case UserGroup::ADMIN:
                $where->compare('group', 'super', '!=');
                break;
            case UserGroup::USER:
                $where->compare('group', 'super', '!=');
                $where->compare('group', 'admin', '!=');
                break;
            case UserGroup::GUEST:
                $where->compare('group', 'guest');
                break;
        }

        $result = $query->execute();

        $roles = [];

        while ($row = $this->connection->fetch($result)) {
            $roles[] = $row['alias'];
        }

        return $roles;
    }
}
