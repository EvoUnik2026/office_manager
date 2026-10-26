<?php

declare(strict_types=1);

namespace App\Domain\Monitoring;

use App\Domain\Office\Room;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class Sensor
{
    private readonly string $id;
    private ?Room $room = null;
    private SensorStatus $status = SensorStatus::Active;

    /**
     * @var Collection<int, Measurement>
     */
    private Collection $measurements;

    public function __construct(
        private string $deviceId,
        private string $name,
        private string $type,
    ) {
        $this->id = self::newId();
        $this->measurements = new ArrayCollection();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function deviceId(): string
    {
        return $this->deviceId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function status(): SensorStatus
    {
        return $this->status;
    }

    public function activate(): void
    {
        $this->status = SensorStatus::Active;
    }

    public function deactivate(): void
    {
        $this->status = SensorStatus::Inactive;
    }

    public function room(): ?Room
    {
        return $this->room;
    }

    public function setRoom(Room $room): void
    {
        $this->room = $room;
    }

    public function assignToRoom(Room $room): void
    {
        $room->assignSensor($this);
    }

    public function moveToRoom(Room $room): void
    {
        $this->room?->removeSensor($this);
        $this->assignToRoom($room);
    }

    public function removeFromRoom(): void
    {
        $this->room = null;
    }

    /**
     * @return Collection<int, Measurement>
     */
    public function measurements(): Collection
    {
        return $this->measurements;
    }

    public function addMeasurement(Measurement $measurement): void
    {
        if (!$this->measurements->contains($measurement)) {
            $this->measurements->add($measurement);
            $measurement->setSensor($this);
        }
    }

    private static function newId(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0F) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3F) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
