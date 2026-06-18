<?php
namespace Pyncer\Snyppet\Organization\Install;

use Pyncer\Database\Table\Column\IntSize;
use Pyncer\Database\Table\Column\TextSize;
use Pyncer\Database\Table\ReferentialAction;
use Pyncer\Database\Value;
use Pyncer\Snyppet\AbstractInstall;

class Install extends AbstractInstall
{
    protected function safeInstall(): bool
    {
        $this->connection->createTable('organization')
            ->serial('id')
            ->char('uid', 36)->index()
            ->int('user_id', IntSize::BIG)->null()->index()
            ->string('mark', 250)->null()->index()
            ->dateTime('insert_date_time')->default(Value::NOW)->index()
            ->dateTime('update_date_time')->null()->index()
            ->string('name', 50)->null()->index()
            ->string('alias', 50)->null()->index()
            ->bool('enabled')->default(false)->index()
            ->bool('deleted')->default(false)->index()
            ->index('#unique', 'uid')->unique()
            ->execute();

        $this->connection->createTable('organization__data')
            ->serial('id')
            ->int('organization_id', IntSize::BIG)->index()
            ->string('key', 50)->index()
            ->string('type', 125)->index()
            ->text('value', TextSize::MEDIUM)
            ->index('#unique', 'organization_id', 'key')->unique()
            ->foreignKey(null, 'organization_id')
                ->references('organization', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->execute();

        $this->connection->createTable('organization__value')
            ->serial('id')
            ->int('organization_id', IntSize::BIG)->index()
            ->string('key', 50)->index()
            ->string('value', 250)
            ->bool('preload')->default(false)->index()
            ->index('#unique', 'organization_id', 'key')->unique()
            ->foreignKey(null, 'organization_id')
                ->references('organization', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->execute();

        $this->connection->createTable('organization__user')
            ->serial('id')
            ->char('uid', 36)->index()
            ->int('organization_id', IntSize::BIG)->index()
            ->int('user_id', IntSize::BIG)->index()
            ->string('mark', 250)->null()->index()
            ->dateTime('insert_date_time')->default(Value::NOW)->index()
            ->dateTime('update_date_time')->null()->index()
            ->enum('group', ['guest', 'user', 'admin', 'super'])->default('user')->index()
            ->string('name', 50)->null()->index()
            ->bool('pending')->default(false)->index()
            ->bool('enabled')->default(false)->index()
            ->index('#unique', 'user_id', 'organization_id')->unique()
            ->index('#unique', 'uid')->unique()
            ->foreignKey(null, 'organization_id')
                ->references('organization', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->foreignKey(null, 'user_id')
                ->references('user', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->execute();

        $this->connection->createTable('organization__user__role')
            ->serial('id')
            ->int('user_id', IntSize::BIG)->index()
            ->int('role_id', IntSize::BIG)->index()
            ->index('#unique', 'user_id', 'role_id')->unique()
            ->foreignKey(null, 'user_id')
                ->references('user', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->foreignKey(null, 'role_id')
                ->references('role', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->execute();

        $this->connection->createTable('organization__token')
            ->serial('id')
            ->int('organization_id', IntSize::BIG)->index()
            ->int('token_id', IntSize::BIG)->index()
            ->index('#unique', 'organization_id', 'token_id')->unique()
            ->foreignKey(null, 'organization_id')
                ->references('organization', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->foreignKey(null, 'token_id')
                ->references('token', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->execute();

        return true;
    }

    protected function safeUninstall(): bool
    {
        if ($this->connection->hasTable('organization__value')) {
            $this->connection->dropTable('organization__value');
        }

        if ($this->connection->hasTable('organization__data')) {
            $this->connection->dropTable('organization__data');
        }

        if ($this->connection->hasTable('organization__token')) {
            $this->connection->dropTable('organization__token');
        }

        if ($this->connection->hasTable('organization__user__role')) {
            $this->connection->dropTable('organization__user__role');
        }

        if ($this->connection->hasTable('organization__user')) {
            $this->connection->dropTable('organization__user');
        }

        if ($this->connection->hasTable('organization')) {
            $this->connection->dropTable('organization');
        }

        return true;
    }

    public function getRequired(): array
    {
        return [
            'access' => '*',
            'role' => '*'
        ];
    }
}
