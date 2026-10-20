<?php

declare(strict_types=1);

namespace App\Domain\Monitoring;

enum SensorStatus: string
{
    case Active = 'ACTIVE';
    case Inactive = 'INACTIVE';
}
