<?php

namespace App\Command;

use App\Accounting\Account\AverageAccount;
use App\Console\StackaCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:list:transaction|read',
    description: 'List the Transactions of a given Asset',
)]
class ListTransactionCommand extends StackaCommand
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
            'Type',
            'Amount',
            'Money',
            'Money Average',
            'A. Amount',
            'A. Money',
            'A. Money Average'
        ], array_map(function($transaction) use ($account) {
            $account->record($transaction);

            return [
                $transaction->getType()->value,
                $transaction->getBalance()->getAmount(),
                $transaction->getBalance()->getMoney(),
                $transaction->getBalance()->getMoneyAverage(),
                $account->getBalance()->getAmount(),
                $account->getBalance()->getMoney(),
                $account->getBalance()->getMoneyAverage(),
            ];
        }, $asset->getTransactions()->toArray()));

        return Command::SUCCESS;
    }
}
