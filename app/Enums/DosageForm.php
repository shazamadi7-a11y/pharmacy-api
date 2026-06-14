<?php

declare(strict_types=1);

namespace App\Enums;

enum DosageForm: string
{
    case Tablet = 'tablet';
    case Capsule = 'capsule';
    case Syrup = 'syrup';
    case Injection = 'injection';
    case Cream = 'cream';
    case Drops = 'drops';
}
