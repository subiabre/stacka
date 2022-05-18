<?php

namespace App\Command;

use App\Accounting\Account\AverageAccount;
use App\Console\StackaCommand;
use App\Entity\Transaction;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:trx:list|read',
    description: 'List the Transactions of a given Asset',
)]
class TransactionListCommand extends StackaCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('asset', InputArgument::REQUIRED, 'The name of the asset that was transacted')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $asset = $this->getAsset($input, $output, 'asset');
        if (!$asset) return Command::FAILURE;

        $account = new AverageAccount($asset);

        $io->table([
            'T. ID',
            'T. Date',
            'T. Type',
            'T. Amount',
            'T. Money',
            'T. Average',
            'A. Amount',
            'A. Money',
            'A. Average'
        ], array_map(function(Transaction $transaction) use ($account) {
            $asset = $transaction->getAsset();

            $account->addTransaction($transaction);

            return [
                $transaction->getId(),
                $transaction->getDateFormatted(),
                $transaction->getType()->value,
                $transaction->getBalance()->getAmount(),
                $transaction->getBalance()->getMoney()->formatTo($asset->getMoneyFormat()),
                $transaction->getBalance()->getMoneyAverage()->formatTo($asset->getMoneyFormat()),
                $account->getBalance()->getAmount(),
                $account->getBalance()->getMoney()->formatTo($asset->getMoneyFormat()),
                $account->getBalance()->getMoneyAverage()->formatTo($asset->getMoneyFormat()),
            ];
        }, $asset->getTransactions()->toArray()));

        return Command::SUCCESS;
    }
}
