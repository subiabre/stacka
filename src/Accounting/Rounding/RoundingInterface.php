<?php

namespace App\Accounting\Rounding;

interface RoundingInterface
{
    public const MESSAGE_ERROR_UNKNOWN = "The key '%s' does not match to any available Rounding name.";

    public static function getName(): string;

    public static function getDescription(): string;

    public static function getValue(): int;
}
