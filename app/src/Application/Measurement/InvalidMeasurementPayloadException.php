<?php

declare(strict_types=1);

namespace App\Application\Measurement;

final class InvalidMeasurementPayloadException extends \RuntimeException
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        string $message,
        private readonly array $payload = [],
    ) {
        parent::__construct($message);
    }

    /**
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
