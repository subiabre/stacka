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
    name: 'app:accounting:list|accountings',
    description: 'List the available accounting methods',
)]
class AccountingListCommand extends StackaCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $accounts = $this->accountLocator->getAccounts();

        $io->listing(array_map(function(AbstractAccount $account) {
            return sprintf("<info>%s</info>\n %s", $account->getName(), $account->getDescription());
        }, \iterator_to_array($accounts)));

        return Command::SUCCESS;
    }
}
