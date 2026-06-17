<?php
namespace Pyncer\Snyppet\Organization\Table\Organization;

use Pyncer\Data\Model\AbstractModel;

class ValueModel extends AbstractModel
{
    public function getOrganizationId(): int
    {
        return $this->get('organization_id');
    }
    public function setOrganizationId(int $value): static
    {
        $this->set('organization_id', $value);
        return $this;
    }

    public function getKey(): string
    {
        return $this->get('key');
    }
    public function setKey(string $value): static
    {
        $this->set('key', $value);
        return $this;
    }

    public function getValue(): ?string
    {
        return $this->get('value');
    }
    public function setValue(?string $value): static
    {
        $this->set('value', $this->nullify($value));
        return $this;
    }

    public function getPreload(): bool
    {
        return $this->get('preload');
    }
    public function setPreload(bool $value): static
    {
        $this->set('preload', $value);
        return $this;
    }

    public static function getDefaultData(): array
    {
        return [
            'id' => 0,
            'organization_id' => 0,
            'key' => '',
            'value' => null,
            'preload' => false,
        ];
    }
}
