<?php

declare(strict_types=1);

namespace App\Domain\Climate;

enum HVACType: string
{
    case Heater = 'HEATER';
    case Cooler = 'COOLER';
    case HeatPump = 'HEAT_PUMP';
    case AirConditioner = 'AIR_CONDITIONER';
    case Other = 'OTHER';
}
