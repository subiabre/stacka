<?php

namespace App\Command;

use App\Console\StackaCommand;
use App\Entity\Asset;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:asset:info|info',
    description: 'Get detailed info about an Asset',
)]
class AssetInfoCommand extends StackaCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('asset', InputArgument::REQUIRED, 'The name of the asset')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $asset = $this->getAsset($input, $output, 'asset');
        if (!$asset) return Command::FAILURE;

        $io->definitionList(
            ['Name' => $asset->getName()],
            ['Transaction count' => $asset->getTransactions()->count()],
            ['Accounting' => $asset->getAccount()->getName()],
            new TableSeparator(),
            ['Date Formatting' => $asset->getDateFormat()],
            new TableSeparator(),
            ['Money Currency' => $asset->getMoneyCurrency()],
            ['Money Formatting' => $asset->getMoneyFormat()],
        );

        return Command::SUCCESS;
    }
}
