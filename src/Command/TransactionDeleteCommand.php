<?php

namespace App\Command;

use App\Console\StackaCommand;
use App\Entity\Transaction;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:trx:delete|erase',
    description: 'Deletes a Transaction',
)]
class TransactionDeleteCommand extends StackaCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the transaction to be deleted')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $id = $input->getArgument('id');

        $transaction = $this->transactionRepository->find($id);
        if (!$transaction) {
            $io->error(sprintf(Transaction::MESSAGE_ERROR_MISSING, $id));
            return Command::FAILURE;
        }

        $confirm = $io->confirm("This is an irreversible action.\n Do you want to proceed?");

        if (!$confirm) return Command::FAILURE;

        $this->entityManager->remove($transaction);
        $this->entityManager->flush();

        $io->success(sprintf(Transaction::MESSAGE_SUCCESS_REMOVED, $id));

        return Command::SUCCESS;
    }
}
