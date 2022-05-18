<?php

namespace App\Command;

use App\Console\StackaCommand;
use App\Entity\Asset;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:asset:delete|delete',
    description: 'Deletes an Asset',
)]
class AssetDeleteCommand extends StackaCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('asset', InputArgument::REQUIRED, 'The name of the asset to be deleted')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $asset = $this->getAsset($input, $output, 'asset');
        if (!$asset) return Command::FAILURE;

        $confirm = $io->confirm(sprintf(
            "The asset '%s' contains %d transactions that will also be deleted.\n Do you want to proceed?",
            $asset->getName(), $asset->getTransactions()->count())
        );

        if (!$confirm) return Command::FAILURE;

        $this->entityManager->remove($asset);
        $this->entityManager->flush();

        $io->success(sprintf(Asset::MESSAGE_SUCCESS_REMOVED, $asset->getName()));

        return Command::SUCCESS;
    }
}
