<?php

namespace App\Accounting\Rounding;

use Brick\Math\RoundingMode;

class RoundingUp implements RoundingInterface
{
    public static function getName(): string
    {
        return 'up';
    }
    
    public static function getDescription(): string
    {
        return 'Rounds away from zero.';
    }

    public static function getValue(): int
    {
        return RoundingMode::UP;
    }
}
