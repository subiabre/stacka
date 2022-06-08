<?php

namespace App\Accounting;

use App\Accounting\Balance\Balance;
use App\Entity\Transaction;
use Brick\Math\BigRational;

/**
 * FIFO accounting assumes the items you sold were the available items you purchased **first**
 */
class FifoAccount extends AbstractAccount
{
    /** @var Balance[] */
    protected array $inventory = [];

    private Balance $sales;

    private BigRational $earnings;

    public function __construct()
    {
        $this->sales = new Balance();
        $this->earnings = BigRational::of(0);
    }

    public static function getName(): string
    {
        return 'fifo';
    }

    public static function getDescription(): string
    {
        return 'The items you sold were the available items you purchased **first**';
    }

    protected function buy(Transaction $transaction)
    {
        $this->inventory = [...$this->inventory, $transaction->getBalance()];
    }

    protected function sell(Transaction $transaction)
    {
        $sold = $transaction->getBalance()->getAmount();
        $gain = $transaction->getBalance()->getMoney();
        $cost = BigRational::of(0);
        
        $this->sales = new Balance(
            $this->sales->getAmount()->plus($sold),
            $this->sales->getMoney()->plus($gain)
        );

        foreach ($this->inventory as $key => $inventory) {
            if ($inventory->getAmount()->isGreaterThanOrEqualTo($sold)) {
                $cost = $inventory->getMoneyAverage()->multipliedBy($sold);

                $available = $inventory->getAmount()->minus($sold);
                $inventory = new Balance($available, $inventory->getMoneyAverage()->multipliedBy($available));
                
                $this->inventory = array_replace($this->inventory, [$key => $inventory]);
                break;
            }

            $cost = $inventory->getMoney();
            $sold = $sold->minus($inventory->getAmount());

            $this->inventory = array_diff_key($this->inventory, [$key]);
        }

        $this->earnings = $this->earnings->plus($gain->minus($cost));
    }

    public function getInventory(): Balance
    {
        $amount = BigRational::of(0);
        $money = BigRational::of(0);

        foreach ($this->inventory as $inventory) {
            $amount = $amount->plus($inventory->getAmount());
            $money = $money->plus($inventory->getMoney());
        }

        return new Balance($amount, $money);
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
