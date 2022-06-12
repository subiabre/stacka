<?php

namespace App\Command;

use App\Console\StackaCommand;
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

        $io->table([],[
            ['<info>Asset</info>'],
            new TableSeparator(),
            ['Name', $asset->getName()],
            ['Transactions', $asset->getTransactions()->count()],
            ['Accounting', $asset->getAccount()->getName()],
            new TableSeparator(),
            ['<info>Date</info>'],
            new TableSeparator(),
            ['Format', $asset->getDateFormat()],
            new TableSeparator(),
            ['<info>Money</info>'],
            new TableSeparator(),
            ['Currency', $asset->getMoneyCurrency()],
            ['Format', $asset->getMoneyFormat()],
            ['Scale', $asset->getMoneyScale()],
            ['Rounding', $asset->getMoneyRounding()->getName()]
        ]);

        return Command::SUCCESS;
    }
}
