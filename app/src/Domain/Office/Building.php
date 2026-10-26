<?php

declare(strict_types=1);

namespace App\Domain\Office;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class Building
{
    private readonly string $id;

    /**
     * @var Collection<int, Room>
     */
    private Collection $rooms;

    public function __construct(
        private string $name,
        private string $address,
    ) {
        $this->id = self::newId();
        $this->rooms = new ArrayCollection();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function address(): string
    {
        return $this->address;
    }

    public function rename(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection<int, Room>
     */
    public function rooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Room $room): void
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms->add($room);
            $room->setBuilding($this);
        }
    }

    public function removeRoom(Room $room): void
    {
        if ($this->rooms->removeElement($room)) {
            $room->removeFromBuilding();
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
