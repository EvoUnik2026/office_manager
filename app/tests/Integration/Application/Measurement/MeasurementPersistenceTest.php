<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\Measurement;

use App\Application\Measurement\MeasurementPayload;
use App\Application\Measurement\MeasurementProcessor;
use App\Entity\Sensor;
use App\Repository\MeasurementRepository;
use App\Tests\Integration\IntegrationTestCase;

final class MeasurementPersistenceTest extends IntegrationTestCase
{
    public function testKnownSensorMeasurementIsPersistedInMariaDb(): void
    {
        $sensor = (new Sensor())
            ->setDeviceId('integration-sensor-1')
            ->setName('Integration Sensor')
            ->setType('temperature_humidity');

        $this->entityManager->persist($sensor);
        $this->entityManager->flush();

        $processor = self::getContainer()->get(MeasurementProcessor::class);
        \assert($processor instanceof MeasurementProcessor);

        $deviceId = $sensor->getDeviceId();
        \assert(null !== $deviceId);

        $processor->process($this->createPayload($deviceId));
        $this->entityManager->clear();

        $measurementRepository = self::getContainer()->get(MeasurementRepository::class);
        \assert($measurementRepository instanceof MeasurementRepository);

        $measurement = $measurementRepository->findOneBy(['sensor' => $sensor->getId()]);

        self::assertNotNull($measurement);
        self::assertSame(21.5, $measurement->getTemperature());
        self::assertSame(55.0, $measurement->getHumidity());
        self::assertSame(1_700_000_000, $measurement->getMeasuredAt()?->getTimestamp());
        self::assertSame($sensor->getId(), $measurement->getSensor()?->getId());
    }

    public function testUnknownSensorDoesNotPersistMeasurement(): void
    {
        $processor = self::getContainer()->get(MeasurementProcessor::class);
        \assert($processor instanceof MeasurementProcessor);

        $processor->process($this->createPayload('unknown-sensor'));

        $measurementCount = (int) $this->entityManager
            ->getConnection()
            ->fetchOne('SELECT COUNT(*) FROM measurement');

        self::assertSame(0, $measurementCount);
    }

    private function createPayload(string $deviceId): MeasurementPayload
    {
        return new MeasurementPayload(
            deviceId: $deviceId,
            temperature: 21.5,
            humidity: 55.0,
            measuredAt: (new \DateTimeImmutable())->setTimestamp(1_700_000_000),
        );
    }
}
