<?php
namespace Pyncer\Snyppet\Organization\Component\Module\Organization;

use Pyncer\App\Identifier as ID;
use Pyncer\Component\Module\AbstractDeleteIndexModule;
use Pyncer\Data\Mapper\MapperInterface;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Snyppet\Organization\Table\Organization\OrganizationMapper;
use Pyncer\Snyppet\Organization\Table\Organization\OrganizationMapperQuery;
use Pyncer\Snyppet\Utility\Component\SoftDeleteTrait;

class DeleteOrganizationItemModule extends AbstractDeleteIndexModule
{
    use SoftDeleteTrait;

    protected function forgeMapper(): MapperInterface
    {
        $connection = $this->get(ID::DATABASE);
        return new OrganizationMapper($connection);
    }

    protected function forgeMapperQuery(): ?MapperQueryInterface
    {
        $connection = $this->get(ID::DATABASE);
        return new OrganizationMapperQuery($connection, $this->request);
    }

    protected function deleteItem(ModelInterface $model): array
    {
        if (!$this->getSoftDelete()) {
            return parent::deleteItem($model);
        }

        $errors = [];

        try {
            $mapper = $this->forgeMapper();
            $model->setDeleted(true);
            $mapper->update($model);
        } catch (QueryException) {
            $errors['general'] = 'delete';
        }

        return $errors;
    }
}
