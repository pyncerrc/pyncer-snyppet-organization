<?php
namespace Pyncer\Snyppet\Organization\Table\Organization;

use Pyncer\Data\MapperQuery\AbstractRequestMapperQuery;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Database\Record\SelectQueryInterface;

use function Pyncer\Array\unset_keys as pyncer_array_unset_keys;

class OrganizationMapperQuery extends AbstractRequestMapperQuery
{
    public function overrideModel(
        ModelInterface $model,
        array $data,
    ): ModelInterface
    {
        if (!$this->getOptions()) {
            return $model;
        }

        if ($this->getOptions()->hasOption('include-organization-values')) {
            $result = $this->getConnection()->select('organization__value')
                ->columns('key', 'value')
                ->where(['organization_id' => $model->getId()])
                ->result();

            $extraData = [];
            foreach ($result as $row) {
                $extraData[$row['key']] = $row['value'];
            }

            $extraData = pyncer_array_unset_keys($extraData, $model->getKeys());
            $model->addExtraData($extraData);
        }

        if ($this->getOptions()->hasOption('include-organization-data')) {
            $result = $this->getConnection()->select('organization__data')
                ->columns('key', 'type', 'value')
                ->where(['organization_id' => $model->getId()])
                ->result();

            $extraData = [];
            foreach ($result as $row) {
                $extraData[$row['key']] = [
                    'type' => $row['type'],
                    'value' => $row['value'],
                ];
            }

            $extraData = pyncer_array_unset_keys($extraData, $model->getKeys());
            $model->addExtraData($extraData);
        }

        return $model;
    }

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

        if ($left === 'enabled' &&
            is_bool($right) &&
            ($operator === '=' || $operator === '!=')
        ) {
            return true;
        }

        if ($left === 'deleted' &&
            is_bool($right) &&
            ($operator === '=' || $operator === '!=')
        ) {
            return true;
        }

        if ($left === 'alias' &&
            is_string($right) &&
            ($operator === '=' || $operator === '!=')
        ) {
            return true;
        }

        return parent::isValidFilter($left, $right, $operator);
    }

    protected function applyFilter(
        SelectQueryInterface $query,
        string $left,
        mixed $right,
        string $operator
    ): SelectQueryInterface
    {
        return parent::applyFilter($query, $left, $right, $operator);
    }

    protected function isValidOption(string $option): bool
    {
        switch ($option) {
            case 'include-organization-data':
            case 'include-organization-values':
                return true;
        }

        return parent::isValidOption($option);
    }

    protected function isValidOrderBy(string $key, string $direction): bool
    {
        switch ($key) {
            case 'name':
            case 'alias':
            case 'enabled':
                return true;
        }

       return parent::isValidOrderBy($key, $direction);
    }
}
