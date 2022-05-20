<?php

namespace App\Accounting\Rounding;

use Brick\Math\RoundingMode;

class RoundingHalfCeiling implements RoundingInterface
{
    public static function getName(): string
    {
        return 'half-ceiling';
    }
    
    public static function getDescription(): string
    {
        return "Rounds towards nearest neighbor unless both neighbors are equidistant, in which case round towards positive infinity.";
    }

    public static function getValue(): int
    {
        return RoundingMode::HALF_CEILING;
    }
}
