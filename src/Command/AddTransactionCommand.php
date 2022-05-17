<?php

namespace App\Command;

use App\Accounting\Balance\Balance;
use App\Console\StackaCommand;
use App\Entity\Transaction;
use Brick\Math\BigDecimal;
use Brick\Money\Context\AutoContext;
use Brick\Money\Money;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:add:transaction|new',
    description: 'Record a new Transaction',
)]
class AddTransactionCommand extends StackaCommand
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
            BigDecimal::of($input->getArgument('amount')),
            Money::of($input->getArgument('money'), $asset->getMoneyCurrency(), new AutoContext())
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
