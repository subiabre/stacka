<?php

namespace App\Command;

use App\Accounting\Account\AverageAccount;
use App\Console\StackaCommand;
use App\Entity\Asset;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:asset:list|assets',
    description: 'List the added Assets',
)]
class AssetListCommand extends StackaCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('asset', InputArgument::OPTIONAL, 'A name to filter assets. You can use the `%` wildcard.', '%%')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $assets = $this->assetRepository->findLikeName($input->getArgument('asset'));

        $io->table([
            'Name',
            'Records',
            'Amount',
            'Money',
            'Average'
        ], array_map(function(Asset $asset) {
            $account = new AverageAccount($asset);

            $account->setTransactions($asset->getTransactions());

            return [
                $asset->getName(),
                count($asset->getTransactions()),
                $account->getBalance()->getAmount(),
                $account->getBalance()->getMoney(),
                $account->getBalance()->getMoneyAverage()
            ];
        }, $assets));

        return Command::SUCCESS;
    }
}
