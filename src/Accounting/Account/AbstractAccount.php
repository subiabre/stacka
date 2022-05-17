<?php

namespace App\Accounting\Account;

use App\Accounting\Balance\Balance;
use App\Accounting\Transaction\TransactionType;
use App\Entity\Asset;
use App\Entity\Transaction;
use Brick\Math\BigDecimal;
use Brick\Money\Context\AutoContext;
use Brick\Money\Money;

/**
 * An accounting `Account` holds an historic of `Transaction` elements and checks the produced `Balance`
 */
abstract class AbstractAccount
{
    /** @var Transaction[] */
    protected array $history = [];

    /** @var Balance[] */
    protected array $inventory = [];

    protected Asset $asset;

    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

    abstract public static function getName();

    public function getHistory(): array
    {
        return $this->history;
    }

    public function getInventory(): array
    {
        return $this->inventory;
    }

    public function getAsset(): Asset
    {
        return $this->asset;
    }

    final public function getBalance(): Balance
    {
        $amount = BigDecimal::of(0);
        $money = Money::of(0, $this->asset->getMoneyCurrency(), new AutoContext());

        foreach ($this->inventory as $balance) {
            $amount = $amount->plus($balance->getAmount());
            $money = $money->plus($balance->getMoney());
        };

        return new Balance($amount, $money);
    }

    final public function record(Transaction $transaction): self
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
