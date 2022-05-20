<?php

namespace App\Accounting\Rounding;

use Brick\Math\RoundingMode;

class RoundingFloor implements RoundingInterface
{
    public static function getName(): string
    {
        return 'floor';
    }
    
    public static function getDescription(): string
    {
        return 'Rounds towards negative infinity.';
    }

    public static function getValue(): int
    {
        return RoundingMode::FLOOR;
    }
}
