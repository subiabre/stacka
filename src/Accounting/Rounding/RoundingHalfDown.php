<?php

namespace App\Accounting\Rounding;

use Brick\Math\RoundingMode;

class RoundingHalfDown implements RoundingInterface
{
    public static function getName(): string
    {
        return 'half-down';
    }
    
    public static function getDescription(): string
    {
        return 'Rounds towards nearest neighbor unless both neighbors are equidistant, in which case round down.';
    }

    public static function getValue(): int
    {
        return RoundingMode::HALF_DOWN;
    }
}
