<?php
namespace Pyncer\Snyppet\Organization\Table\Organization;

use DateTime;
use DateTimeInterface;
use Pyncer\Data\Model\AbstractModel;

use function Pyncer\uid as pyncer_uid;
use function Pyncer\date_time as pyncer_date_time;

use const Pyncer\DATE_TIME_FORMAT as PYNCER_DATE_TIME_FORMAT;

class OrganizationModel extends AbstractModel
{
    public function getUid(): string
    {
        return $this->get('uid');
    }
    public function setUid(string $value): static
    {
        $this->set('uid', $value);
        return $this;
    }

    public function getUserId(): int
    {
        return $this->get('user_id');
    }
    public function setUserId(int $value): static
    {
        $this->set('user_id', $value);
        return $this;
    }

    public function getMark(): ?string
    {
        return $this->get('mark');
    }
    public function setMark(?string $value): static
    {
        $this->set('mark', $this->nullify($value));
        return $this;
    }

    public function getInsertDateTime(): DateTime
    {
        $value = $this->get('insert_date_time');
        return pyncer_date_time($value);
    }
    public function setInsertDateTime(string|DateTimeInterface $value): static
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->format(PYNCER_DATE_TIME_FORMAT);
        }
        $this->set('insert_date_time', $value);
        return $this;
    }

    public function getUpdateDateTime(): ?DateTime
    {
        $value = $this->get('update_date_time');
        return pyncer_date_time($value);
    }
    public function setUpdateDateTime(null|string|DateTimeInterface $value): static
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->format(PYNCER_DATE_TIME_FORMAT);
        }
        $this->set('update_date_time', $this->nullify($value));
        return $this;
    }

    public function getName(): ?string
    {
        return $this->get('name');
    }
    public function setName(?string $value): static
    {
        $this->set('name', $this->nullify($value));
        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->get('alias');
    }
    public function setAlias(?string $value): static
    {
        $this->set('alias', $this->nullify($value));
        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->get('enabled');
    }
    public function setEnabled(bool $value): static
    {
        $this->set('enabled', $value);
        return $this;
    }

    public function getDeleted(): bool
    {
        return $this->get('deleted');
    }
    public function setDeleted(bool $value): static
    {
        $this->set('deleted', $value);
        return $this;
    }

    public static function getDefaultData(): array
    {
        $dateTime = pyncer_date_time()->format(PYNCER_DATE_TIME_FORMAT);

        return [
            'id' => 0,
            'uid' => pyncer_uid(),
            'user_id' => 0,
            'mark' => null,
            'insert_date_time' => $dateTime,
            'update_date_time' => null,
            'name' => null,
            'alias' => null,
            'enabled' => false,
            'deleted' => false,
        ];
    }
}
