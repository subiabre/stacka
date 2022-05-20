<?php

namespace App\Accounting\Rounding;

class RoundingLocator
{
    private iterable $roundings;

    public function __construct(iterable $roundings)
    {
        $this->roundings = $roundings;
    }

    public function getRoundings(): iterable
    {
        return $this->roundings;
    }

    public function filterByName(string $name): ?RoundingInterface
    {
        foreach ($this->roundings as $rounding) {
            if ($name === $rounding::getName()) return $rounding;
        }

        return null;
    }
}
