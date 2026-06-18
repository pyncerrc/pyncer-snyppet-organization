<?php
namespace Pyncer\Snyppet\Organization\Table\Organization;

use Pyncer\Data\Mapper\AbstractMapper;
use Pyncer\Data\Mapper\MapperResultInterface;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Snyppet\Organization\Table\Organization\OrganizationModel;
use Pyncer\Snyppet\Organization\Table\Organization\OrganizationMapperQuery;

class OrganizationMapper extends AbstractMapper
{
    public function getTable(): string
    {
        return 'organization';
    }

    public function forgeModel(iterable $data = []): ModelInterface
    {
        return new OrganizationModel($data);
    }

    public function isValidModel(ModelInterface $model): bool
    {
        return ($model instanceof OrganizationModel);
    }

    public function isValidMapperQuery(MapperQueryInterface $mapperQuery): bool
    {
        return ($mapperQuery instanceof OrganizationMapperQuery);
    }

    public function selectByTokenId(
        int $tokenId,
        ?MapperQueryInterface $mapperQuery = null,
    ): ?ModelInterface
    {
        $row = $this->forgeSelectQuery($mapperQuery)
            ->join('organization__token', 'organization_id', 'id')
            ->getWhere()
            ->compare(['organization__token', 'token_id'], $tokenId)
            ->getQuery()
            ->row();

        return $this->forgeModelFromData($row);
    }

    public function selectByUid(
        string $uid,
        ?MapperQueryInterface $mapperQuery = null,
    ): ?ModelInterface
    {
        return $this->selectByColumns(
            [
                'uid' => $uid,
            ],
            $mapperQuery
        );
    }

    public function selectAllByUserId(
        int $userId,
        ?MapperQueryInterface $mapperQuery = null,
    ): MapperResultInterface
    {
        $result = $this->forgeSelectQuery($mapperQuery)
            ->join('organization__user', 'organization_id', 'id')
            ->getWhere()
            ->compare(['organization__user', 'user_id'], $userId)
            ->getQuery()
            ->result(['count' => 500]);

        return $this->forgeResult($result, $mapperQuery);
    }

    public function selectAllByUserUid(
        string $userUid,
        ?MapperQueryInterface $mapperQuery = null,
    ): MapperResultInterface
    {
        $result = $this->forgeSelectQuery($mapperQuery)
            ->join('organization__user', 'organization_id', 'id')
            ->join('user', 'id', ['organization__user', 'user_id'])
            ->getWhere()
            ->compare(['user', 'uid'], $userUid)
            ->getQuery()
            ->result(['count' => 500]);

        return $this->forgeResult($result, $mapperQuery);
    }
}
