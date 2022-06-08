<?php

namespace App\Accounting;

use App\Accounting\Balance\Balance;
use App\Entity\Transaction;
use Brick\Math\BigRational;

/**
 * Average accounting assumes the items you sold had an homogenous cost calculated by average
 */
class AverageAccount extends AbstractAccount
{
    private Balance $inventory;
    private Balance $sales;
    private BigRational $earnings;

    public function __construct()
    {
        $this->inventory = new Balance();
        $this->sales = new Balance();
        $this->earnings = BigRational::of(0);
    }

    public static function getName(): string
    {
        return 'average';
    }

    public static function getDescription(): string
    {
        return 'The items you sold had an homogenous cost calculated by average of the available inventory';
    }

    protected function buy(Transaction $transaction)
    {
        $this->inventory = new Balance(
            $this->inventory->getAmount()->plus($transaction->getBalance()->getAmount()),
            $this->inventory->getMoney()->plus($transaction->getBalance()->getMoney())
        );
    }

    protected function sell(Transaction $transaction)
    {
        $sold = $transaction->getBalance()->getAmount();
        $gain = $transaction->getBalance()->getMoney();
        $cost = $this->inventory->getMoneyAverage()->multipliedBy($sold);

        $available = $this->inventory->getAmount()->minus($sold);
        $this->inventory = new Balance(
            $available,
            $this->inventory->getMoneyAverage()->multipliedBy($available)
        );

        $this->sales = new Balance(
            $this->sales->getAmount()->plus($sold),
            $this->sales->getMoney()->plus($gain)
        );

        $this->earnings = $this->earnings->plus($gain->minus($cost));
    }

    public function getInventory(): Balance
    {
        return $this->inventory;
    }

    public function getSales(): Balance
    {
        return $this->sales;
    }

    public function getEarnings(): BigRational
    {
        return $this->earnings;
    }
}
