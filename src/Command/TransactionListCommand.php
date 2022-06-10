<?php

namespace App\Command;

use App\Console\StackaCommand;
use App\Entity\Transaction;
use App\Service\AssetFormatterService;
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
            ->addArgument('offset', InputArgument::OPTIONAL, 'Distance from the start to skip entries', 0)
            ->addArgument('limit', InputArgument::OPTIONAL, 'Maximum number of rows to output', 10)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $asset = $this->getAsset($input, $output, 'asset');
        if (!$asset) return Command::FAILURE;

        $account = $asset->getAccount();
        $formatter = new AssetFormatterService($asset);

        $transactions = array_map(function(Transaction $transaction) use ($account, $formatter) {
            $account->addTransaction($transaction);

            return [
                $transaction->getAsset()->getTransactions()->indexOf($transaction),
                $transaction->getId(),
                $formatter->date($transaction->getDate()),
                $transaction->getType()->value,
                $formatter->amount($transaction->getBalance()->getAmount()),
                $formatter->money($transaction->getBalance()->getMoney()),
                $formatter->money($transaction->getBalance()->getMoneyAverage()),
                $formatter->amount($account->getInventory()->getAmount()),
                $formatter->money($account->getInventory()->getMoney()),
                $formatter->money($account->getInventory()->getMoneyAverage()),
                $formatter->amount($account->getSales()->getAmount()),
                $formatter->money($account->getSales()->getMoney()),
                $formatter->money($account->getEarnings())
            ];
        }, $asset->getTransactions()->toArray());

        $io->table([
            'T. #',
            'T. ID',
            'T. Date',
            'T. Type',
            'T. Amount',
            'T. Money',
            'T. Average',
            'I. Amount',
            'I. Money',
            'I. Average',
            'S. Amount',
            'S. Money',
            'Earnings'
        ], array_slice($transactions, $input->getArgument('offset'), $input->getArgument('limit')));

        return Command::SUCCESS;
    }
}
