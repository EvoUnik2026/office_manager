<?php

declare(strict_types=1);

namespace App\Domain\Office;

use App\Domain\Climate\HVAC;
use App\Domain\Monitoring\Sensor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class Room
{
    private readonly string $id;
    private ?Building $building = null;
    private ?HVAC $hvac = null;

    /**
     * @var Collection<int, Sensor>
     */
    private Collection $sensors;

    /**
     * @var Collection<int, Asset>
     */
    private Collection $assets;

    public function __construct(
        private string $name,
        private RoomType $type,
        private ?string $number = null,
        private ?string $floor = null,
    ) {
        $this->id = self::newId();
        $this->sensors = new ArrayCollection();
        $this->assets = new ArrayCollection();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): RoomType
    {
        return $this->type;
    }

    public function number(): ?string
    {
        return $this->number;
    }

    public function floor(): ?string
    {
        return $this->floor;
    }

    public function building(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(Building $building): void
    {
        $this->building = $building;
    }

    public function removeFromBuilding(): void
    {
        $this->building = null;
    }

    /**
     * @return Collection<int, Sensor>
     */
    public function sensors(): Collection
    {
        return $this->sensors;
    }

    public function assignSensor(Sensor $sensor): void
    {
        if (!$this->sensors->contains($sensor)) {
            $this->sensors->add($sensor);
            $sensor->setRoom($this);
        }
    }

    public function removeSensor(Sensor $sensor): void
    {
        if ($this->sensors->removeElement($sensor)) {
            $sensor->removeFromRoom();
        }
    }

    public function hvac(): ?HVAC
    {
        return $this->hvac;
    }

    public function assignHvac(HVAC $hvac): void
    {
        $this->hvac = $hvac;
        $hvac->setRoom($this);
    }

    public function removeHvac(): void
    {
        if ($this->hvac === null) {
            return;
        }

        $hvac = $this->hvac;
        $this->hvac = null;
        $hvac->removeFromRoom();
    }

    /**
     * @return Collection<int, Asset>
     */
    public function assets(): Collection
    {
        return $this->assets;
    }

    public function addAsset(Asset $asset): void
    {
        if (!$this->assets->contains($asset)) {
            $this->assets->add($asset);
            $asset->setRoom($this);
        }
    }

    public function removeAsset(Asset $asset): void
    {
        if ($this->assets->removeElement($asset)) {
            $asset->removeFromRoom();
        }
    }

    private static function newId(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
