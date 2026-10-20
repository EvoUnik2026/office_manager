<?php

declare(strict_types=1);

namespace App\Domain\Climate;

enum HVACStatus: string
{
    case On = 'ON';
    case Off = 'OFF';
}
