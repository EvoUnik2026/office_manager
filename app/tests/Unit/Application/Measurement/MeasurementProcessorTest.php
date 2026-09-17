<?php

declare(strict_types=1);

namespace App\Tests\Application\Measurement;

use App\Application\Measurement\MeasurementPayload;
use App\Application\Measurement\MeasurementProcessor;
use App\Entity\Measurement;
use App\Entity\Sensor;
use App\Repository\SensorRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class MeasurementProcessorTest extends TestCase
{
    public function testKnownSensorMeasurementIsPersisted(): void
    {
        $sensor = (new Sensor())->setDeviceId('sensor-1');
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $sensorRepository = $this->createMock(SensorRepository::class);

        $sensorRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['deviceId' => 'sensor-1'])
            ->willReturn($sensor);

        $entityManager
            ->expects(self::once())
            ->method('persist')
            ->with(self::callback(
                static function (Measurement $measurement) use ($sensor): bool {
                    return $measurement->getSensor() === $sensor
                        && 21.5 === $measurement->getTemperature()
                        && 55.0 === $measurement->getHumidity()
                        && 1_700_000_000 === $measurement->getMeasuredAt()?->getTimestamp();
                },
            ));

        $entityManager
            ->expects(self::once())
            ->method('flush');

        $processor = new MeasurementProcessor(
            $sensorRepository,
            $entityManager,
            new NullLogger(),
        );

        $processor->process($this->createPayload());

        self::assertCount(1, $sensor->getMeasurements());
    }

    public function testUnknownSensorMeasurementIsNotPersisted(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $sensorRepository = $this->createMock(SensorRepository::class);

        $sensorRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['deviceId' => 'sensor-1'])
            ->willReturn(null);

        $entityManager
            ->expects(self::never())
            ->method('persist');

        $entityManager
            ->expects(self::never())
            ->method('flush');

        $processor = new MeasurementProcessor(
            $sensorRepository,
            $entityManager,
            new NullLogger(),
        );

        $processor->process($this->createPayload());
    }

    public function testShutdownRequestedMeasurementIsSkipped(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $sensorRepository = $this->createMock(SensorRepository::class);

        $sensorRepository
            ->expects(self::never())
            ->method('findOneBy');

        $entityManager
            ->expects(self::never())
            ->method('persist');

        $entityManager
            ->expects(self::never())
            ->method('flush');

        $processor = new MeasurementProcessor(
            $sensorRepository,
            $entityManager,
            new NullLogger(),
        );

        $processor->requestShutdown();
        $processor->process($this->createPayload());
    }

    private function createPayload(): MeasurementPayload
    {
        return new MeasurementPayload(
            deviceId: 'sensor-1',
            temperature: 21.5,
            humidity: 55.0,
            measuredAt: (new \DateTimeImmutable())->setTimestamp(1_700_000_000),
        );
    }
}
