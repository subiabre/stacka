<?php

namespace App\Accounting\Rounding;

use Brick\Math\RoundingMode;

class RoundingHalfEven implements RoundingInterface
{
    public static function getName(): string
    {
        return 'half-even';
    }
    
    public static function getDescription(): string
    {
        return "Rounds towards nearest neighbor unless both neighbors are equidistant, in which case round towards the even neighbor.";
    }

    public static function getValue(): int
    {
        return RoundingMode::HALF_EVEN;
    }
}
