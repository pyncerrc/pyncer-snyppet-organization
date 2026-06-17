<?php
namespace Pyncer\Snyppet\Organization\Table\User;

use Pyncer\Data\Validation\AbstractValidator;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Snyppet\Access\Table\User\UserMapper;
use Pyncer\Snyppet\Organization\Table\Organization\OrganizationMapper;
use Pyncer\Validation\Rule\BoolRule;
use Pyncer\Validation\Rule\DateTimeRule;
use Pyncer\Validation\Rule\EmailRule;
use Pyncer\Validation\Rule\EnumRule;
use Pyncer\Validation\Rule\PhoneRule;
use Pyncer\Validation\Rule\RequiredRule;
use Pyncer\Validation\Rule\StringRule;
use Pyncer\Validation\Rule\UidRule;

class UserValidator extends AbstractValidator
{
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);

        $this->addRules(
            'uid',
            new RequiredRule(UidRule::EMPTY),
            new UidRule(),
            new StringRule(
                maxLength: 36,
            ),
        );

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
            'user_id',
            new RequiredRule(IntRule::EMPTY),
            new IntRule(
                minValue: 0,
            ),
            new IdRule(
                mapper: new UserMapper($this->getConnection()),
            ),
        );

        $this->addRules(
            'mark',
            new StringRule(
                maxLength: 250,
                allowNull: true,
            ),
        );

        $this->addRules(
            'insert_date_time',
            new RequiredRule(DateTimeRule::EMPTY),
            new DateTimeRule(),
        );

        $this->addRules(
            'update_date_time',
            new DateTimeRule(
                allowNull: true
            ),
        );

        $this->addRules(
            'group',
            new RequiredRule(),
            new EnumRule([
                'guest', 'user', 'admin', 'super'
            ]),
        );

        $this->addRules(
            'name',
            new StringRule(
                maxLength: 50,
                allowNull: true,
            ),
        );

        $this->addRules(
            'pending',
            new BoolRule(),
        );

        $this->addRules(
            'enabled',
            new BoolRule(),
        );
    }
}
