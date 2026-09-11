<?php

namespace App\Command;

use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:mqtt:consume',
    description: 'Consumes IoT measurements from the MQTT broker',
)]
class MqttConsumeCommand extends Command
{
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        $host = 'mosquitto';
        $port = 1883;
        $clientId = 'symfony-consumer';

        $mqtt = new MqttClient(
            $host,
            $port,
            $clientId
        );

        $connectionSettings = new ConnectionSettings();

        $io->info(sprintf(
            'Connecting to MQTT broker %s:%d...',
            $host,
            $port
        ));

        $mqtt->connect($connectionSettings, true);

        $io->success('Connected to MQTT broker.');

        $topic = 'iot/device/+/measurement';

        $mqtt->subscribe(
            $topic,
            function (string $topic, string $message) use ($io): void {
                $io->writeln(sprintf(
                    '[MQTT] %s',
                    $message
                ));
            },
            0
        );

        $io->success(sprintf(
            'Subscribed to topic: %s',
            $topic
        ));

        $mqtt->loop(true);

        return Command::SUCCESS;
    }
}