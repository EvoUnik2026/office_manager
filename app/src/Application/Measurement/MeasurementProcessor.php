<?php

namespace App\Application\Measurement;

use App\Entity\Measurement;
use App\Repository\SensorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class MeasurementProcessor
{
    public function __construct(
        private SensorRepository $sensorRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
    ) {
    }

    /**
     *
     * @param array<string, mixed> $data
     */
    public function process(array $data): void
    {
        $deviceId = $data['device_id'] ?? null;

        if (!is_string($deviceId) || $deviceId === '') {
            $this->logger->warning(
                'Ignoring MQTT measurement without a valid device_id.',
                ['payload' => $data]
            );

            return;
        }

        $sensor = $this->sensorRepository->findOneBy([
            'deviceId' => $deviceId,
        ]);

        if ($sensor === null) {
            $this->logger->warning(
                'Ignoring MQTT measurement from unknown device.',
                ['device_id' => $deviceId]
            );

            return;
        }

        if (
            !isset($data['temperature']) ||
            !is_numeric($data['temperature']) ||
            !isset($data['humidity']) ||
            !is_numeric($data['humidity']) ||
            !isset($data['timestamp']) ||
            !is_numeric($data['timestamp'])
        ) {
            $this->logger->warning(
                'Ignoring invalid MQTT measurement.',
                [
                    'device_id' => $deviceId,
                    'payload' => $data,
                ]
            );

            return;
        }

        $measurement = new Measurement();

        $measurement
            ->setTemperature((float) $data['temperature'])
            ->setHumidity((float) $data['humidity'])
            ->setMeasuredAt(
                (new \DateTimeImmutable())->setTimestamp(
                    (int) $data['timestamp']
                )
            );

        $sensor->addMeasurement($measurement);

        $this->entityManager->persist($measurement);
        $this->entityManager->flush();

        $this->logger->info(
            'MQTT measurement stored.',
            [
                'device_id' => $deviceId,
                'temperature' => $measurement->getTemperature(),
                'humidity' => $measurement->getHumidity(),
            ]
        );
    }
}