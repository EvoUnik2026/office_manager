<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Measurement\InvalidMeasurementPayloadException;
use App\Application\Measurement\MeasurementPayloadValidator;
use App\Application\Measurement\MeasurementProcessor;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:mqtt-consume',
    description: 'Consumes IoT measurements from the MQTT broker',
)]
class MqttConsumeCommand extends Command implements SignalableCommandInterface
{
    private ?MqttClient $mqtt = null;
    private ?SymfonyStyle $io = null;

    public function __construct(
        private MeasurementProcessor $measurementProcessor,
        private MeasurementPayloadValidator $measurementPayloadValidator,
    ) {
        parent::__construct();
    }

    /**
     * @return array<int>
     */
    public function getSubscribedSignals(): array
    {
        return [\SIGTERM, \SIGINT];
    }

    public function handleSignal(int $signal, int|false $previousExitCode = 0): int|false
    {
        $this->io?->warning(sprintf(
            'Received signal %d, requesting graceful shutdown...',
            $signal,
        ));

        $this->measurementProcessor->requestShutdown();
        $this->mqtt?->interrupt();

        return false;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $io = new SymfonyStyle($input, $output);
        $this->io = $io;

        $host = 'mosquitto';
        $port = 1883;
        $clientId = 'symfony-consumer';
        $topic = 'iot/device/+/measurement';

        $this->mqtt = new MqttClient(
            $host,
            $port,
            $clientId,
        );

        $connectionSettings = new ConnectionSettings();

        $io->info(sprintf(
            'Connecting to MQTT broker %s:%d...',
            $host,
            $port,
        ));

        $this->mqtt->connect($connectionSettings, true);

        $io->success('Connected to MQTT broker.');

        $this->mqtt->subscribe(
            $topic,
            function (string $topic, string $message): void {
                $this->io?->writeln(sprintf('[MQTT] %s', $message));

                $data = json_decode($message, true);

                if (!is_array($data)) {
                    $this->io?->warning(sprintf(
                        'Ignoring non-JSON MQTT payload on topic %s.',
                        $topic,
                    ));

                    return;
                }

                try {
                    $payload = $this->measurementPayloadValidator->validate($data);

                    $this->measurementProcessor->process($payload);
                } catch (InvalidMeasurementPayloadException $exception) {
                    $this->io?->warning(sprintf(
                        'Ignoring invalid MQTT measurement on topic %s: %s',
                        $topic,
                        $exception->getMessage(),
                    ));

                    return;
                }
            },
            0,
        );

        $io->success(sprintf(
            'Subscribed to topic: %s',
            $topic,
        ));

        try {
            $this->mqtt->loop(true);
        } finally {
            $this->disconnectCleanly($topic);
        }

        $io->success('MQTT consumer stopped.');

        return Command::SUCCESS;
    }

    private function disconnectCleanly(string $topic): void
    {
        if (null === $this->mqtt) {
            return;
        }

        try {
            $this->mqtt->unsubscribe($topic);
        } catch (\Throwable $exception) {
            $this->io?->warning(sprintf(
                'Could not unsubscribe from MQTT topic: %s',
                $exception->getMessage(),
            ));
        }

        try {
            $this->mqtt->disconnect();
        } catch (\Throwable $exception) {
            $this->io?->warning(sprintf(
                'Could not disconnect from MQTT broker: %s',
                $exception->getMessage(),
            ));
        }
    }
}
