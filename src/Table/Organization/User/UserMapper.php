<?php
namespace Pyncer\Snyppet\Organization\Table\Organization\User;

use Pyncer\Data\Mapper\AbstractMapper;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Snyppet\Organization\Table\Organization\User\UserModel;
use Pyncer\Snyppet\Organization\Table\Organization\User\UserMapperQuery;

class UserMapper extends AbstractMapper
{
    public function getTable(): string
    {
        return 'organization__user';
    }

    public function forgeModel(iterable $data = []): ModelInterface
    {
        return new UserModel($data);
    }

    public function isValidModel(ModelInterface $model): bool
    {
        return ($model instanceof UserModel);
    }

    public function isValidMapperQuery(MapperQueryInterface $mapperQuery): bool
    {
        return ($mapperQuery instanceof UserMapperQuery);
    }
}
