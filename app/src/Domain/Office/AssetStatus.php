<?php

declare(strict_types=1);

namespace App\Domain\Office;

enum AssetStatus: string
{
    case Active = 'ACTIVE';
    case Retired = 'RETIRED';
}
