<?php
namespace Pyncer\Snyppet\Organization\Table\Organization;

use Pyncer\Data\Validation\AbstractValidator;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Snyppet\Organization\Table\Organization\OrganizationMapper;
use Pyncer\Validation\Rule\BoolRule;
use Pyncer\Validation\Rule\IdRule;
use Pyncer\Validation\Rule\IntRule;
use Pyncer\Validation\Rule\RequiredRule;
use Pyncer\Validation\Rule\StringRule;

class ValueValidator extends AbstractValidator
{
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);

        $this->addRules(
            'organization_id',
            new RequiredRule(IntRule::EMPTY),
            new IntRule(
                minValue: 0,
            ),
            new IdRule(
                mapper: new OrganizationMapper($this->getConnection()),
            ),
        );

        $this->addRules(
            'key',
            new RequiredRule(),
            new StringRule(
                maxLength: 50,
            ),
        );

        $this->addRules(
            'value',
            new RequiredRule(),
            new StringRule(
                maxLength: 250,
            ),
        );

        $this->addRules(
            'preload',
            new BoolRule(),
        );
    }
}
