<?php

namespace App\Accounting;

class AccountLocator
{
    /** @var AbstractAccount[] */
    private iterable $accounts;

    public function __construct(iterable $accounts)
    {
        $this->accounts = $accounts;
    }

    public function getAccounts(): iterable
    {
        return $this->accounts;
    }

    public function filterByName(string $name): ?AbstractAccount
    {
        foreach ($this->accounts as $account) {
            if ($name === $account::getName()) return $account;
        }

        return null;
    }
}
