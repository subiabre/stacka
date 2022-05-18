<?php

namespace App\Accounting;

use App\Accounting\Balance\Balance;
use App\Entity\Transaction;

/**
 * Average accounting assumes the items you sold had an homogenous cost calculated by average
 */
class AverageAccount extends FifoAccount
{
    public static function getName(): string
    {
        return 'average';
    }

    public static function getDescription(): string
    {
        return 'The items you sold had an homogenous cost calculated by average of the available inventory';
    }

    protected function sale(Transaction $transaction): array
    {
        $sold = $transaction->getBalance()->getAmount();
        $cost = $this->getBalance()->getMoneyAverage()->multipliedBy($sold);

        $amount = $this->getBalance()->getAmount()->minus($sold);
        $money = $this->getBalance()->getMoney()->minus($cost);

        return [new Balance($amount, $money)];
    }
}
