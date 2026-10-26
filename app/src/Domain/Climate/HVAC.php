<?php

declare(strict_types=1);

namespace App\Domain\Climate;

use App\Domain\Office\Room;

final class HVAC
{
    private readonly string $id;
    private ?Room $room = null;
    private HVACStatus $status = HVACStatus::Off;

    public function __construct(
        private string $deviceId,
        private string $name,
        private HVACType $type,
    ) {
        $this->id = self::newId();
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

    public function type(): HVACType
    {
        return $this->type;
    }

    public function status(): HVACStatus
    {
        return $this->status;
    }

    public function turnOn(): void
    {
        $this->status = HVACStatus::On;
    }

    public function turnOff(): void
    {
        $this->status = HVACStatus::Off;
    }

    public function room(): ?Room
    {
        return $this->room;
    }

    public function setRoom(Room $room): void
    {
        $this->room = $room;
    }

    public function installInRoom(Room $room): void
    {
        $room->assignHvac($this);
    }

    public function moveToRoom(Room $room): void
    {
        $this->room?->removeHvac();
        $this->installInRoom($room);
    }

    public function removeFromRoom(): void
    {
        $this->room = null;
    }

    private static function newId(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0F) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3F) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
