<?php

namespace App\Accounting;

use App\Entity\Transaction;

/**
 * LIFO accounting assumes the items you sold were the available items you purchased **last**
 */
class LifoAccount extends FifoAccount
{
    public static function getName(): string
    {
        return 'lifo';
    }

    public static function getDescription(): string
    {
        return 'The items you sold were the available items you purchased **last**';
    }
    
    protected function sale(Transaction $transaction): array
    {
        $this->inventory = array_reverse($this->inventory);
        
        return parent::sale($transaction);
    }
}
