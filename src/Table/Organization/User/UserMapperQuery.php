<?php
namespace Pyncer\Snyppet\Organization\Table\Organization\User;

use Pyncer\Data\MapperQuery\AbstractRequestMapperQuery;

class UserMapperQuery extends AbstractRequestMapperQuery
{
    protected function isValidFilter(
        string $left,
        mixed $right,
        string $operator,
    ): bool
    {
        if ($left === 'uid' &&
            is_string($right) &&
            ($operator === '=' || $operator === '!=')
        ) {
            return true;
        }

        if ($left === 'organization_id' &&
            is_int($right) &&
            ($operator === '=' || $operator === '!=')
        ) {
            return true;
        }

        if ($left === 'user_id' &&
            is_int($right) &&
            ($operator === '=' || $operator === '!=')
        ) {
            return true;
        }

        if ($left === 'enabled' &&
            is_bool($right) &&
            ($operator === '=' || $operator === '!=')
        ) {
            return true;
        }

        if ($left === 'pending' &&
            is_bool($right) &&
            ($operator === '=' || $operator === '!=')
        ) {
            return true;
        }

        return parent::isValidFilter($left, $right, $operator);
    }
}
