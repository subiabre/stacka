<?php

namespace App\Accounting\Account;

use App\Entity\Transaction;

/**
 * LIFO accounting assumes the items you sold were the available items you purchased **last**
 */
class LifoAccount extends FifoAccount
{
    public static function getName()
    {
        return 'lifo';
    }
    
    protected function sale(Transaction $transaction): array
    {
        $this->inventory = array_reverse($this->inventory);
        
        return parent::sale($transaction);
    }
}
