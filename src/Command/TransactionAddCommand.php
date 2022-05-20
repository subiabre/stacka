<?php

namespace App\Command;

use App\Accounting\Balance\Balance;
use App\Console\StackaCommand;
use App\Entity\Transaction;
use Brick\Math\BigRational;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:trx:add|new',
    description: 'Record a new Transaction',
)]
class TransactionAddCommand extends StackaCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('type', InputArgument::REQUIRED, 'The type of this transaction')
            ->addArgument('asset', InputArgument::REQUIRED, 'The name of the asset that was transacted')
            ->addArgument('amount', InputArgument::REQUIRED, 'The amount of the asset that was transacted')
            ->addArgument('money', InputArgument::REQUIRED, 'The monetary value of the amount transacted')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $asset = $this->getAsset($input, $output, 'asset');
        if (!$asset) return Command::FAILURE;

        $type = $this->getTransactionType($input, $output, 'type');
        if (!$type) return Command::FAILURE;

        $balance = new Balance(
            BigRational::of($input->getArgument('amount')),
            BigRational::of($input->getArgument('money'))
        );
        
        $transaction = new Transaction();
        $transaction
            ->setType($type)
            ->setAsset($asset)
            ->setBalance($balance);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
