<?php

namespace App\Accounting;

use App\Accounting\Balance\Balance;
use App\Accounting\Transaction\TransactionType;
use App\Entity\Transaction;
use Brick\Math\BigDecimal;
use Brick\Money\Money;
use Doctrine\Common\Collections\Collection;

/**
 * An accounting `Account` holds an historic of `Transaction` elements and checks the produced `Balance`
 */
abstract class AbstractAccount
{
    public const MESSAGE_ERROR_UNKNOWN = "The key '%s' does not match to any available Accounting name.";

    /** @var Transaction[] */
    protected array $history = [];

    /** @var Balance[] */
    protected array $inventory = [];

    protected string $currency;

    abstract public static function getName(): string;

    abstract public static function getDescription(): string;

    public function getHistory(): array
    {
        return $this->history;
    }

    public function getInventory(): array
    {
        return $this->inventory;
    }

    final public function getBalance(): ?Balance
    {
        $amount = BigDecimal::of(0);
        $money = BigDecimal::of(0);

        foreach ($this->inventory as $balance) {
            $amount = $amount->plus($balance->getAmount());
            $money = $money->plus($balance->getMoney());
        };

        return new Balance($amount, $money);
    }

    final public function setTransactions(Collection $transactions): self
    {
        foreach ($transactions->toArray() as $transaction) {
            $this->addTransaction($transaction);
        }

        return $this;
    }

    final public function addTransaction(Transaction $transaction): self
    {
        $this->history = [...$this->history, $transaction];

        switch ($transaction->getType()) {
            case TransactionType::Buy:
                $this->inventory = $this->buy($transaction);
                break;
            
            case TransactionType::Sale:
                $this->inventory = $this->sale($transaction);
                break;
        }

        return $this;
    }

    /**
     * Process a `Transaction` of Buy type into the account
     * @return array The resulting inventory
     */
    protected function buy(Transaction $transaction): array
    {
        return [...$this->inventory, $transaction->getBalance()];
    }

    /**
     * Process a `Transaction` of Sale type into the account
     * @return array The resulting inventory
     */
    abstract protected function sale(Transaction $transaction): array;
}
