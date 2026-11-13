<?php
namespace Pyncer\Snyppet\Organization\Table\Organization\User;

use Pyncer\Data\Mapper\AbstractRelationMapper;

class RoleRelationMapper extends AbstractRelationMapper
{
    public function getTable(): string
    {
        return 'organization__user__role';
    }

    public function getParentIdColumn(): string
    {
        return 'organization_user_id';
    }

    public function getChildIdColumn(): string
    {
        return 'role_id';
    }
}
