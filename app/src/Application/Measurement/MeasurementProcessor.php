<?php

declare(strict_types=1);

namespace App\Application\Measurement;

use App\Entity\Measurement;
use App\Repository\SensorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class MeasurementProcessor
{
    private bool $shutdownRequested = false;

    public function __construct(
        private SensorRepository $sensorRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
    ) {}

    public function requestShutdown(): void
    {
        if ($this->shutdownRequested) {
            return;
        }

        $this->shutdownRequested = true;

        $this->logger->info(
            'Measurement processor shutdown requested.',
        );
    }

    public function process(MeasurementPayload $payload): void
    {
        if ($this->shutdownRequested) {
            $this->logger->info(
                'Skipping MQTT measurement because shutdown was requested.',
                [
                    'device_id' => $payload->deviceId,
                ],
            );

            return;
        }

        $sensor = $this->sensorRepository->findOneBy([
            'deviceId' => $payload->deviceId,
        ]);

        if (null === $sensor) {
            $this->logger->warning(
                'Ignoring MQTT measurement from unknown device.',
                [
                    'device_id' => $payload->deviceId,
                ],
            );

            return;
        }

        $measurement = new Measurement();

        $measurement
            ->setTemperature($payload->temperature)
            ->setHumidity($payload->humidity)
            ->setMeasuredAt($payload->measuredAt);

        $sensor->addMeasurement($measurement);

        $this->entityManager->persist($measurement);
        $this->entityManager->flush();

        $this->logger->info(
            'MQTT measurement stored.',
            [
                'device_id' => $payload->deviceId,
                'temperature' => $measurement->getTemperature(),
                'humidity' => $measurement->getHumidity(),
            ],
        );
    }
}
