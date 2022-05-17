<?php

namespace App\Accounting\Transaction;

/**
 * A `Transaction` can be either a `Buy` or a `Sale`\
 * A **Buy** will increase the Balance in inventory\
 * A **Sale** will decrease the Balance in inventory
 */
enum TransactionType: string
{
    case Buy = 'buy';
    case Sale = 'sale';
}
