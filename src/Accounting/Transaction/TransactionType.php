<?php

namespace App\Accounting\Transaction;

/**
 * A `Transaction` can be either a `Buy` or a `Sale`\
 * A **Buy** will increase the Balance in inventory\
 * A **Sale** will decrease the Balance in inventory
 */
enum TransactionType: string
{
    public const MESSAGE_ERROR_UNKNOWN = "The type '%s' is not a valid Transaction type.";

    case Buy = 'buy';
    case Sale = 'sale';
}
