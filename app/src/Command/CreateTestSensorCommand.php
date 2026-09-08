<?php

namespace App\Command;

use App\Entity\Sensor;
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

        $sensor = new Sensor();

        $sensor->setDeviceId('sensor-001');
        $sensor->setName('Living Room Sensor');
        $sensor->setType('temperature_humidity');

        $this->entityManager->persist($sensor);
        $this->entityManager->flush();

        $io->success(sprintf(
            'Sensor created successfully with ID: %d',
            $sensor->getId()
        ));

        return Command::SUCCESS;
    }
}