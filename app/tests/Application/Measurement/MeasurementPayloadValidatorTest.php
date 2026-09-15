<?php

declare(strict_types=1);

namespace App\Tests\Application\Measurement;

use App\Application\Measurement\InvalidMeasurementPayloadException;
use App\Application\Measurement\MeasurementPayload;
use App\Application\Measurement\MeasurementPayloadValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class MeasurementPayloadValidatorTest extends TestCase
{
    private MeasurementPayloadValidator $validator;

    protected function setUp(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $this->validator = new MeasurementPayloadValidator($validator);
    }

    public function testValidPayloadIsAccepted(): void
    {
        $payload = $this->validator->validate([
            'device_id' => 'sensor-1',
            'temperature' => 21.5,
            'humidity' => 55,
            'timestamp' => 1_700_000_000,
        ]);

        self::assertInstanceOf(MeasurementPayload::class, $payload);
        self::assertSame('sensor-1', $payload->deviceId);
        self::assertSame(21.5, $payload->temperature);
        self::assertSame(55.0, $payload->humidity);
        self::assertSame(1_700_000_000, $payload->measuredAt->getTimestamp());
    }

    public function testNumericStringsAreAccepted(): void
    {
        $payload = $this->validator->validate([
            'device_id' => 'sensor-1',
            'temperature' => '21.5',
            'humidity' => '55',
            'timestamp' => '1700000000',
        ]);

        self::assertSame(21.5, $payload->temperature);
    }

    /**
     * @param array<string, mixed> $data
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('invalidPayloadProvider')]
    public function testInvalidPayloadIsRejected(array $data): void
    {
        $this->expectException(InvalidMeasurementPayloadException::class);

        $this->validator->validate($data);
    }

    public function testUnexpectedFieldsAreRejected(): void
    {
        $payload = [
            'device_id' => 'sensor-1',
            'temperature' => 21.5,
            'humidity' => 55,
            'timestamp' => 1_700_000_000,
            'unexpected' => 'value',
        ];

        try {
            $this->validator->validate($payload);

            self::fail('Expected InvalidMeasurementPayloadException was not thrown.');
        } catch (InvalidMeasurementPayloadException $exception) {
            self::assertStringContainsString(
                'unexpected',
                strtolower($exception->getMessage())
            );
        }
    }

    /**
     * @return iterable<string, array{0: array<string, mixed>}>
     */
    public static function invalidPayloadProvider(): iterable
    {
        yield 'missing device_id' => [[
            'temperature' => 21.5,
            'humidity' => 55,
            'timestamp' => 1_700_000_000,
        ]];

        yield 'blank device_id' => [[
            'device_id' => '',
            'temperature' => 21.5,
            'humidity' => 55,
            'timestamp' => 1_700_000_000,
        ]];

        yield 'non-string device_id' => [[
            'device_id' => 42,
            'temperature' => 21.5,
            'humidity' => 55,
            'timestamp' => 1_700_000_000,
        ]];

        yield 'missing temperature' => [[
            'device_id' => 'sensor-1',
            'humidity' => 55,
            'timestamp' => 1_700_000_000,
        ]];

        yield 'non-numeric temperature' => [[
            'device_id' => 'sensor-1',
            'temperature' => 'hot',
            'humidity' => 55,
            'timestamp' => 1_700_000_000,
        ]];

        yield 'missing humidity' => [[
            'device_id' => 'sensor-1',
            'temperature' => 21.5,
            'timestamp' => 1_700_000_000,
        ]];

        yield 'non-numeric humidity' => [[
            'device_id' => 'sensor-1',
            'temperature' => 21.5,
            'humidity' => 'damp',
            'timestamp' => 1_700_000_000,
        ]];

        yield 'missing timestamp' => [[
            'device_id' => 'sensor-1',
            'temperature' => 21.5,
            'humidity' => 55,
        ]];

        yield 'non-numeric timestamp' => [[
            'device_id' => 'sensor-1',
            'temperature' => 21.5,
            'humidity' => 55,
            'timestamp' => 'yesterday',
        ]];

        yield 'boolean temperature' => [[
            'device_id' => 'sensor-1',
            'temperature' => true,
            'humidity' => 55,
            'timestamp' => 1_700_000_000,
        ]];

        yield 'boolean humidity' => [[
            'device_id' => 'sensor-1',
            'temperature' => 21.5,
            'humidity' => false,
            'timestamp' => 1_700_000_000,
        ]];

        yield 'boolean timestamp' => [[
            'device_id' => 'sensor-1',
            'temperature' => 21.5,
            'humidity' => 55,
            'timestamp' => true,
        ]];

        yield 'fractional timestamp' => [[
            'device_id' => 'sensor-1',
            'temperature' => 21.5,
            'humidity' => 55,
            'timestamp' => 1_700_000_000.5,
        ]];

        yield 'empty temperature' => [[
            'device_id' => 'sensor-1',
            'temperature' => '',
            'humidity' => 55,
            'timestamp' => 1_700_000_000,
        ]];

        yield 'empty humidity' => [[
            'device_id' => 'sensor-1',
            'temperature' => 21.5,
            'humidity' => '',
            'timestamp' => 1_700_000_000,
        ]];

        yield 'empty timestamp' => [[
            'device_id' => 'sensor-1',
            'temperature' => 21.5,
            'humidity' => 55,
            'timestamp' => '',
        ]];
    }

    public function testExceptionCarriesRawPayloadForLogging(): void
    {
        $rawPayload = [
            'device_id' => '',
            'temperature' => 21.5,
            'humidity' => 55,
            'timestamp' => 1_700_000_000,
        ];

        try {
            $this->validator->validate($rawPayload);
            self::fail('Expected InvalidMeasurementPayloadException was not thrown.');
        } catch (InvalidMeasurementPayloadException $exception) {
            self::assertSame($rawPayload, $exception->getPayload());
        }
    }
}
