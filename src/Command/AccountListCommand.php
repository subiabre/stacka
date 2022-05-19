<?php

namespace App\Command;

use App\Accounting\AbstractAccount;
use App\Console\StackaCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:account:list|accountings',
    description: 'List the available accounting models',
)]
class AccountingListCommand extends StackaCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $accounts = $this->accountLocator->getAccounts();

        $io->table([
            'Name',
            'Description'
        ], array_map(function(AbstractAccount $account) {
            return [
                $account::getName(),
                $account::getDescription()
            ];
        }, \iterator_to_array($accounts)));

        return Command::SUCCESS;
    }
}
