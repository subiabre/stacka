<?php

namespace App\Accounting\Rounding;

use Brick\Math\RoundingMode;

class RoundingHalfUp implements RoundingInterface
{
    public static function getName(): string
    {
        return 'half-up';
    }
    
    public static function getDescription(): string
    {
        return 'Rounds towards nearest neighbor unless both neighbors are equidistant, in which case round up.';
    }

    public static function getValue(): int
    {
        return RoundingMode::HALF_UP;
    }
}
