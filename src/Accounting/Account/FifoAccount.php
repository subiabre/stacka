<?php

namespace App\Accounting\Account;

use App\Accounting\Inventory\Balance;
use App\Entity\Transaction;
use Brick\Math\BigDecimal;

/**
 * FIFO accounting assumes the items you sold were the available items you purchased **first**
 */
class FifoAccount extends AbstractAccount
{
    public static function getName()
    {
        return 'fifo';
    }

    protected function sale(Transaction $transaction)
    {
        $sold = $transaction->getBalance()->getAmount();
        foreach ($this->balances as $key => $balance) {
            if ($sold->isZero()) {
                break;
            }

            if ($balance->getAmount()->isGreaterThanOrEqualTo($sold)) {
                $amount = $balance->getAmount()->minus($sold);
                $sold = BigDecimal::of(0);
            } else {
                $amount = BigDecimal::of(0);
                $sold = $sold->minus($balance->getAmount());
            }

            $money = $balance->getMoneyAverage()->multipliedBy($amount);

            $this->balances = array_replace($this->balances, [$key => new Balance($amount, $money)]);
        }
    }
}
