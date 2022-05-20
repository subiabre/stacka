<?php

namespace App\Accounting\Rounding;

use Brick\Math\RoundingMode;

class RoundingDown implements RoundingInterface
{
    public static function getName(): string
    {
        return 'down';
    }
    
    public static function getDescription(): string
    {
        return 'Rounds towards zero.';
    }

    public static function getValue(): int
    {
        return RoundingMode::DOWN;
    }
}
