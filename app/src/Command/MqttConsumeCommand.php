<?php

namespace App\Command;

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
    ) {
        parent::__construct();
    }

    public function getSubscribedSignals(): array
    {
        return [\SIGTERM, \SIGINT];
    }

    public function handleSignal(int $signal, int|false $previousExitCode = 0): int|false
    {
        $this->io?->warning(sprintf(
            'Received signal %d, requesting graceful shutdown...',
            $signal
        ));

        $this->measurementProcessor->requestShutdown();
        $this->mqtt?->interrupt();

        return false;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $this->io = new SymfonyStyle($input, $output);

        $host = 'mosquitto';
        $port = 1883;
        $clientId = 'symfony-consumer';
        $topic = 'iot/device/+/measurement';

        $this->mqtt = new MqttClient(
            $host,
            $port,
            $clientId
        );

        $connectionSettings = new ConnectionSettings();

        $this->io->info(sprintf(
            'Connecting to MQTT broker %s:%d...',
            $host,
            $port
        ));

        $this->mqtt->connect($connectionSettings, true);

        $this->io->success('Connected to MQTT broker.');

        $this->mqtt->subscribe(
            $topic,
            function (string $topic, string $message): void {
                if ($this->measurementProcessor->isShutdownRequested()) {
                    return;
                }

                $this->io?->writeln(sprintf('[MQTT] %s', $message));

                $data = json_decode($message, true);

                if (!is_array($data)) {
                    $this->io?->warning(sprintf(
                        'Ignoring non-JSON MQTT payload on topic %s.',
                        $topic
                    ));

                    return;
                }

                $this->measurementProcessor->process($data);
            },
            0
        );

        $this->io->success(sprintf(
            'Subscribed to topic: %s',
            $topic
        ));

        try {
            $this->mqtt->loop(true);
        } finally {
            $this->disconnectCleanly($topic);
        }

        $this->io->success('MQTT consumer stopped.');

        return Command::SUCCESS;
    }

    private function disconnectCleanly(string $topic): void
    {
        if ($this->mqtt === null) {
            return;
        }

        try {
            $this->mqtt->unsubscribe($topic);
        } catch (\Throwable $exception) {
            $this->io?->warning(sprintf(
                'Could not unsubscribe from MQTT topic: %s',
                $exception->getMessage()
            ));
        }

        try {
            $this->mqtt->disconnect();
        } catch (\Throwable $exception) {
            $this->io?->warning(sprintf(
                'Could not disconnect from MQTT broker: %s',
                $exception->getMessage()
            ));
        }
    }
}
