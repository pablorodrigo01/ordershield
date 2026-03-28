<?php

namespace App\Enums;

enum RiskClassificationEnum: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
}
