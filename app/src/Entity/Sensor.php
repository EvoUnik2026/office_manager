<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Measurement;

#[ORM\Entity]
class Sensor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $deviceId = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Measurement>
     */
    #[ORM\OneToMany(targetEntity: Measurement::class, mappedBy: 'sensor')]
    private Collection $measurements;

    public function __construct()
    {
        $now = new \DateTimeImmutable();

        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->measurements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeviceId(): ?string
    {
        return $this->deviceId;
    }

    public function setDeviceId(string $deviceId): static
    {
        $this->deviceId = $deviceId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Measurement>
     */
    public function getMeasurements(): Collection
    {
        return $this->measurements;
    }

    public function addMeasurement(Measurement $measurement): static
    {
        if (!$this->measurements->contains($measurement)) {
            $this->measurements->add($measurement);
            $measurement->setSensor($this);
        }

        return $this;
    }

    public function removeTemprature(Measurement $temprature): static
    {
        if ($this->temprature->removeElement($temprature)) {
            // set the owning side to null (unless already changed)
            if ($temprature->getSensor() === $this) {
                $temprature->setSensor(null);
            }
        }

        return $this;
    }
}
