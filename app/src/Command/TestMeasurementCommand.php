<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Measurement\MeasurementPayloadValidator;
use App\Application\Measurement\MeasurementProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-measurement',
    description: 'Tests the measurement processing flow',
)]
class TestMeasurementCommand extends Command
{
    public function __construct(
        private MeasurementProcessor $measurementProcessor,
        private MeasurementPayloadValidator $measurementPayloadValidator,
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $io = new SymfonyStyle($input, $output);

        $payload = [
            'device_id' => 'sensor-001',
            'temperature' => 22.5,
            'humidity' => 48.5,
            'timestamp' => time(),
        ];

        $io->info('Processing test measurement...');
        $io->text(json_encode($payload, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        $measurementPayload = $this->measurementPayloadValidator->validate($payload);
        $this->measurementProcessor->process($measurementPayload);

        $io->success('Measurement processed successfully.');

        return Command::SUCCESS;
    }
}
