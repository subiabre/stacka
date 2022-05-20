<?php

namespace App\Accounting\Rounding;

use Brick\Math\RoundingMode;

class RoundingCeiling implements RoundingInterface
{
    public static function getName(): string
    {
        return 'ceiling';
    }
    
    public static function getDescription(): string
    {
        return 'Rounds towards positive infinity.';
    }

    public static function getValue(): int
    {
        return RoundingMode::CEILING;
    }
}
