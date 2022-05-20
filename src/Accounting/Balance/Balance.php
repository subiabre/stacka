<?php

namespace App\Accounting\Balance;

use Brick\Math\BigRational;

/**
 * A `Balance` holds a relationship between an homogenous inventory amount and a monetary amount\
 * e.g: Amount: 12, Money: $120 = 12 Units x $10 cost per unit 
 */
class Balance
{
    private BigRational $amount;

    private BigRational $money;

    /**
     * @param BigRational $amount Any unit value
     * @param BigRational $money The total money value for the given amount
     */
    public function __construct(BigRational $amount, BigRational $money)
    {
        $this->amount = $amount;
        $this->money = $money;
    }

    public function getAmount(): BigRational
    {
        return $this->amount;
    }

    public function getMoney(): BigRational
    {
        return $this->money;
    }

    public function getMoneyAverage(): BigRational
    {
        return !$this->amount->isZero() 
            ? $this->money->toBigRational()->dividedBy($this->amount->toBigRational())
            : $this->amount
            ;
    }
}
