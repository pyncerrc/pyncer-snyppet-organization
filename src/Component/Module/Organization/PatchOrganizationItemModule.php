<?php
namespace Pyncer\Snyppet\Organization\Component\Module\Organization;

use Pyncer\App\Identifier as ID;
use Pyncer\Component\Module\AbstractPatchItemModule;
use Pyncer\Data\Mapper\MapperInterface;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Data\Validation\ValidatorInterface;
use Pyncer\Snyppet\Organization\Table\Organization\OrganizationMapper;
use Pyncer\Snyppet\Organization\Table\Organization\OrganizationMapperQuery;
use Pyncer\Snyppet\Organization\Table\Organization\OrganizationValidator;

use function Pyncer\date_time as pyncer_date_time;

class PatchOrganizationItemModule extends AbstractPatchItemModule
{
    protected function getRequiredItemData(): array
    {
        $data = parent::getRequiredItemData();

        $data['update_date_time'] = pyncer_date_time();

        return $data;
    }

    protected function forgeValidator(): ?ValidatorInterface
    {
        $connection = $this->get(ID::DATABASE);
        return new OrganizationValidator($connection);
    }

    protected function forgeMapper(): MapperInterface
    {
        $connection = $this->get(ID::DATABASE);
        return new OrganizationMapper($connection);
    }

    protected function forgeMapperQuery(): ?MapperQueryInterface
    {
        $connection = $this->get(ID::DATABASE);
        return new OrganizationMapperQuery($connection);
    }
}
