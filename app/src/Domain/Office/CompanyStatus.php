<?php

declare(strict_types=1);

namespace App\Domain\Office;

enum CompanyStatus: string
{
    case Active = 'ACTIVE';
    case Archived = 'ARCHIVED';
}
