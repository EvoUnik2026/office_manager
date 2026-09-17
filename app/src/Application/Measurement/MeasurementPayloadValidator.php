<?php

declare(strict_types=1);

namespace App\Application\Measurement;

use Symfony\Component\Validator\Validator\ValidatorInterface;

final class MeasurementPayloadValidator
{
    /**
     * @var list<string>
     */
    private const ALLOWED_FIELDS = ['device_id', 'temperature', 'humidity', 'timestamp'];

    public function __construct(
        private ValidatorInterface $validator,
    ) {}

    /**
     * @param array<string, mixed> $data
     *
     * @throws InvalidMeasurementPayloadException
     */
    public function validate(array $data): MeasurementPayload
    {
        $this->assertFieldTypes($data);

        $payload = new MeasurementPayload(
            deviceId: (string) $data['device_id'],
            temperature: (float) $data['temperature'],
            humidity: (float) $data['humidity'],
            measuredAt: (new \DateTimeImmutable())->setTimestamp((int) $data['timestamp']),
        );

        $violations = $this->validator->validate($payload);

        if (count($violations) > 0) {
            $messages = [];

            foreach ($violations as $violation) {
                $messages[] = sprintf(
                    '%s: %s',
                    $violation->getPropertyPath(),
                    $violation->getMessage(),
                );
            }

            throw new InvalidMeasurementPayloadException(sprintf('Invalid measurement payload: %s', implode('; ', $messages)), $data);
        }

        return $payload;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws InvalidMeasurementPayloadException
     */
    private function assertFieldTypes(array $data): void
    {
        $unexpectedFields = array_diff(array_keys($data), self::ALLOWED_FIELDS);

        if ([] !== $unexpectedFields) {
            throw new InvalidMeasurementPayloadException(sprintf('Measurement payload contains unexpected field(s): %s.', implode(', ', $unexpectedFields)), $data);
        }

        if (!isset($data['device_id']) || !is_string($data['device_id']) || '' === $data['device_id']) {
            throw new InvalidMeasurementPayloadException('Measurement payload is missing a valid "device_id" string.', $data);
        }

        foreach (['temperature', 'humidity'] as $field) {
            if (!isset($data[$field]) || !is_numeric($data[$field])) {
                throw new InvalidMeasurementPayloadException(sprintf('Measurement payload is missing a valid numeric "%s".', $field), $data);
            }
        }

        if (
            !isset($data['timestamp'])
            || !is_numeric($data['timestamp'])
            || 0.0 !== fmod((float) $data['timestamp'], 1.0)
        ) {
            throw new InvalidMeasurementPayloadException('Measurement payload is missing a valid whole-number "timestamp".', $data);
        }
    }
}
