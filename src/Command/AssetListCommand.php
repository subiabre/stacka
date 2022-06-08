<?php

namespace App\Command;

use App\Console\StackaCommand;
use App\Entity\Asset;
use App\Service\AssetFormatterService;
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
            'I. Amount',
            'I. Money',
            'I. Average',
            'S. Money',
            'Earnings'
        ], array_map(function(Asset $asset) {
            $account = $asset->getAccount();
            $formatter = new AssetFormatterService($asset);

            $account->setTransactions($asset->getTransactions());

            return [
                $asset->getName(),
                count($asset->getTransactions()),
                $account->getInventory()->getAmount(),
                $formatter->money($account->getInventory()->getMoney()),
                $formatter->money($account->getInventory()->getMoneyAverage()),
                $formatter->money($account->getSales()->getMoney()),
                $formatter->money($account->getEarnings())
            ];
        }, $assets));

        return Command::SUCCESS;
    }
}
