<?php

namespace App\Accounting\Account;

use App\Accounting\Inventory\Balance;
use App\Accounting\Transaction\TransactionType;
use App\Entity\Transaction;
use Brick\Math\BigDecimal;
use Brick\Money\Money;

/**
 * An accounting `Account` holds an historic of `Transaction` elements and checks the produced `Balance`
 */
abstract class AbstractAccount
{
    /** @var Transaction[] */
    protected array $history = [];

    /** @var Balance[] */
    protected array $balances = [];

    abstract public static function getName();

    public function getHistory(): array
    {
        return $this->history;
    }

    final public function getBalance(): Balance
    {
        $amount = BigDecimal::of(0);
        $money = Money::of(0, 'USD');

        foreach ($this->balances as $balance) {
            $amount = $amount->plus($balance->getAmount());
            $money = $money->plus($balance->getMoney());
        };

        return new Balance($amount, $money);
    }

    public function getBalances(): array
    {
        return $this->balances;
    }

    final public function record(Transaction $transaction): self
    {
        $this->history = [...$this->history, $transaction];

        switch ($transaction->getType()) {
            case TransactionType::Buy:
                $this->buy($transaction);
                break;
            
            case TransactionType::Sale:
                $this->sale($transaction);
                break;
        }

        return $this;
    }

    /**
     * Process a `Transaction` of Buy type into the account
     */
    final protected function buy(Transaction $transaction)
    {
        $this->balances = [...$this->balances, $transaction->getBalance()];
    }

    /**
     * Process a `Transaction` of Sale type into the account
     */
    abstract protected function sale(Transaction $transaction);
}
