<?php
namespace Pyncer\Snyppet\Organization\Component\Module\Organization;

use Pyncer\App\Identifier as ID;
use Pyncer\Component\Module\AbstractGetIndexModule;
use Pyncer\Data\Mapper\MapperInterface;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Snyppet\Organization\Table\Organization\OrganizationMapper;
use Pyncer\Snyppet\Organization\Table\Organization\OrganizationMapperQuery;
use Pyncer\Snyppet\Organization\Table\Organization\OrganizationModel;

class GetOrganizationIndexModule extends AbstractGetIndexModule
{
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
}
