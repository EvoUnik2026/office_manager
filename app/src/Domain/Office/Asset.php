<?php

declare(strict_types=1);

namespace App\Domain\Office;

final class Asset
{
    private readonly string $id;
    private ?Room $room = null;
    private AssetStatus $status = AssetStatus::Active;

    public function __construct(
        private string $assetNumber,
        private string $name,
        private string $type,
    ) {
        $this->id = self::newId();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function assetNumber(): string
    {
        return $this->assetNumber;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function status(): AssetStatus
    {
        return $this->status;
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
        $room->addAsset($this);
    }

    public function moveToRoom(Room $room): void
    {
        $this->room?->removeAsset($this);
        $this->assignToRoom($room);
    }

    public function removeFromRoom(): void
    {
        $this->room = null;
    }

    public function retire(): void
    {
        $this->status = AssetStatus::Retired;
        $this->room?->removeAsset($this);
    }

    private static function newId(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0F) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3F) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
