<?php
namespace Pyncer\Snyppet\Organization\Table\Organization;

use Pyncer\Snyppet\Organization\Table\Organization\ValueModel;
use Pyncer\Data\Mapper\AbstractMapper;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Data\Mapper\MapperResultInterface;

class ValueMapper extends AbstractMapper
{
    public function getTable(): string
    {
        return 'organization__value';
    }

    public function forgeModel(iterable $data = []): ModelInterface
    {
        return new ValueModel($data);
    }

    public function isValidModel(ModelInterface $model): bool
    {
        return ($model instanceof ValueModel);
    }

    public function selectAllPreloaded(
        int $organizationId,
        ?MapperQueryInterface $mapperQuery = null
    ): MapperResultInterface
    {
        return $this->selectAllByColumns(
            [
                'organization_id' => $organizationId,
                'preload' => true
            ],
            $mapperQuery
        );
    }

    public function selectByKey(
        int $organizationId,
        string $key,
        ?MapperQueryInterface $mapperQuery = null
    ): ?ModelInterface
    {
        return $this->selectByColumns(
            [
                'organization_id' => $organizationId,
                'key' => $key,
            ],
            $mapperQuery,
        );
    }

    public function selectAllByKeys(
        int $organizationId,
        array $keys,
        ?MapperQueryInterface $mapperQuery = null
    ): MapperResultInterface
    {
        return $this->selectAllByColumns(
            [
                'organization_id' => $organizationId,
                'key' => $keys,
            ],
            $mapperQuery,
        );
    }
}
