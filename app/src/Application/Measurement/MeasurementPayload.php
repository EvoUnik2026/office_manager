<?php

declare(strict_types=1);

namespace App\Application\Measurement;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class MeasurementPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $deviceId,
        #[Assert\Type('numeric')]
        public float $temperature,
        #[Assert\Type('numeric')]
        public float $humidity,
        public \DateTimeImmutable $measuredAt,
    ) {}
}
