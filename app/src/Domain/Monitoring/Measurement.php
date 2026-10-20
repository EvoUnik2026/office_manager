<?php

declare(strict_types=1);

namespace App\Domain\Monitoring;

final class Measurement
{
    private readonly string $id;
    private ?Sensor $sensor = null;

    public function __construct(
        private float $temperature,
        private float $humidity,
        private \DateTimeImmutable $measuredAt,
    ) {
        $this->id = self::newId();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function temperature(): float
    {
        return $this->temperature;
    }

    public function humidity(): float
    {
        return $this->humidity;
    }

    public function measuredAt(): \DateTimeImmutable
    {
        return $this->measuredAt;
    }

    public function sensor(): ?Sensor
    {
        return $this->sensor;
    }

    public function setSensor(Sensor $sensor): void
    {
        $this->sensor = $sensor;
    }

    private static function newId(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
