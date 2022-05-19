<?php

namespace App\Accounting\Balance;

use Brick\Math\BigDecimal;
use Brick\Money\Money;

/**
 * A `Balance` holds a relationship between an homogenous inventory amount and a monetary amount\
 * e.g: Amount: 12, Money: $120 = 12 Units x $10 cost per unit 
 */
class Balance
{
    private BigDecimal $amount;

    private BigDecimal $money;

    /**
     * @param BigDecimal $amount Any unit value
     * @param BigDecimal $money The total money value for the given amount
     */
    public function __construct(BigDecimal $amount, BigDecimal $money)
    {
        $this->amount = $amount;
        $this->money = $money;
    }

    public function getAmount(): BigDecimal
    {
        return $this->amount;
    }

    public function getMoney(): BigDecimal
    {
        return $this->money;
    }

    public function getMoneyAverage(): BigDecimal
    {
        return !$this->amount->isZero() 
            ? $this->money->dividedBy($this->amount)
            : $this->amount
            ;
    }
}
