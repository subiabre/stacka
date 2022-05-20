<?php

namespace App\Accounting;

use App\Accounting\Balance\Balance;
use App\Entity\Transaction;
use Brick\Math\BigDecimal;

/**
 * FIFO accounting assumes the items you sold were the available items you purchased **first**
 */
class FifoAccount extends AbstractAccount
{
    public static function getName(): string
    {
        return 'fifo';
    }

    public static function getDescription(): string
    {
        return 'The items you sold were the available items you purchased **first**';
    }

    protected function sale(Transaction $transaction): array
    {
        $inventory = $this->getInventory();

        $sold = $transaction->getBalance()->getAmount();
        foreach ($inventory as $key => $balance) {
            if ($sold->isZero()) {
                break;
            }

            if ($balance->getAmount()->isGreaterThanOrEqualTo($sold)) {
                $amount = $balance->getAmount()->minus($sold);
                $money = $balance->getMoneyAverage()->multipliedBy($amount);
                $sold = BigDecimal::of(0);

                $inventory = array_replace($inventory, [$key => new Balance($amount, $money)]);
            } else {
                $sold = $sold->minus($balance->getAmount());

                $inventory = [...array_slice($inventory, 0, $key), ...array_slice($inventory, $key + 1)];
            }
        }

        return $inventory;
    }
}
