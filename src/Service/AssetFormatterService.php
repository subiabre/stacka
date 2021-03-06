<?php

namespace App\Service;

use App\Entity\Asset;
use Brick\Math\BigNumber;
use Brick\Money\Context\CustomContext;
use Brick\Money\Money;

class AssetFormatterService
{
    private Asset $asset;

    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

    public function date(\DateTimeInterface $date)
    {
        $formatter = new \IntlDateFormatter(
            $this->asset->getDateFormat(),
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::SHORT,
            $date->getTimezone()
        );

        return $formatter->format($date);
    }

    private function toCurrency(BigNumber $money)
    {
        return Money::of(
            $money,
            $this->asset->getMoneyCurrency(),
            new CustomContext($this->asset->getMoneyScale()),
            $this->asset->getMoneyRounding()->getValue()
        );
    }

    public function money(BigNumber $money)
    {
        return $this->toCurrency($money)->formatTo($this->asset->getMoneyFormat());
    }

    public function amount(BigNumber $amount)
    {
        return $amount->toBigDecimal();
    }
}
