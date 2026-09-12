<?php

namespace App\Entity;

use App\Repository\MeasurementRepository;
use App\Entity\Sensor;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeasurementRepository::class)]
class Measurement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'measurements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sensor $sensor = null;

    #[ORM\Column]
    private ?float $temperature = null;

    #[ORM\Column]
    private ?float $humidity = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $measuredAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSensor(): ?Sensor
    {
        return $this->sensor;
    }

    public function setSensor(?Sensor $sensor): static
    {
        $this->sensor = $sensor;

        return $this;
    }
    
    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(float $temprature): static
    {
        $this->temperature = $temprature;

        return $this;
    }

    public function getHumidity(): ?float
    {
        return $this->humidity;
    }

    public function setHumidity(float $humidity): static
    {
        $this->humidity = $humidity;

        return $this;
    }

    public function getMeasuredAt(): ?\DateTimeImmutable
    {
        return $this->measuredAt;
    }

    public function setMeasuredAt(\DateTimeImmutable $measuredAt): static
    {
        $this->measuredAt = $measuredAt;

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
}
