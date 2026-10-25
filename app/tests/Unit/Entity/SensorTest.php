<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Domain\Monitoring\SensorStatus;
use App\Entity\Sensor;
use PHPUnit\Framework\TestCase;

final class SensorTest extends TestCase
{
    public function testNewSensorDefaultsToInactive(): void
    {
        $sensor = new Sensor();

        self::assertSame(SensorStatus::Inactive, $sensor->getStatus());
    }

    public function testStatusCanBeChanged(): void
    {
        $sensor = new Sensor();

        $sensor->setStatus(SensorStatus::Active);

        self::assertSame(SensorStatus::Active, $sensor->getStatus());
    }
}