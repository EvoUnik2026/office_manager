<?php

namespace App\Command;

use App\Entity\Sensor;
use App\Entity\Measurement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-test-sensor',
    description: 'Creates a test sensor',
)]
class CreateTestSensorCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        // Create sensor
        $sensor = new Sensor();

        $sensor->setDeviceId('sensor-001');
        $sensor->setName('Living Room Sensor');
        $sensor->setType('temperature_humidity');

        $measurement = new Measurement();

        $measurement->setTemprature(22.5);
        $measurement->setHumidity(48.5);
        $measurement->setMeasuredAt(new \DateTimeImmutable());

        $sensor->addMeasurement($measurement);

        $this->entityManager->persist($sensor);
        $this->entityManager->persist($measurement);

        $this->entityManager->flush();

        $io->success(sprintf(
            'Sensor #%d with measurement #%d created successfully.',
            $sensor->getId(),
            $measurement->getId()
        ));

        return Command::SUCCESS;
    }
}