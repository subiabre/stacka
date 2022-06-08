<?php

namespace App\Accounting;

use App\Accounting\Balance\Balance;
use App\Accounting\Transaction\TransactionType;
use App\Entity\Transaction;
use Brick\Math\BigRational;
use Doctrine\Common\Collections\Collection;

/**
 * An accounting `Account` holds an historic of `Transaction` elements and checks the produced `Balance`
 */
abstract class AbstractAccount
{
    public const MESSAGE_ERROR_UNKNOWN = "The key '%s' does not match to any available Accounting name.";

    /** @var Transaction[] */
    protected array $history = [];

    abstract public static function getName(): string;

    abstract public static function getDescription(): string;

    /**
     * @return Transaction[]
     */
    public function getHistory(): array
    {
        return $this->history;
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
                $this->buy($transaction);
                break;
            
            case TransactionType::Sale:
                $this->sell($transaction);
                break;
        }

        return $this;
    }

    /**
     * Process a `Transaction` of Buy type into the account
     */
    abstract protected function buy(Transaction $transaction);

    /**
     * Process a `Transaction` of Sale type into the account
     */
    abstract protected function sell(Transaction $transaction);

    /**
     * @return Balance The Balance of available units and their gross value (cost)
     */
    abstract public function getInventory(): Balance;

    /**
     * @return Balance The Balance of sold units and their gross value (gains)
     */
    abstract public function getSales(): Balance;

    /**
     * @return BigRational The gross difference between the sale gains and their cost
     */
    abstract public function getEarnings(): BigRational;
}
