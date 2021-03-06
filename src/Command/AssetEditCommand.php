<?php

namespace App\Command;

use App\Console\StackaCommand;
use App\Entity\Asset;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:asset:edit|edit',
    description: 'Edit an Asset',
)]
class AssetEditCommand extends StackaCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('asset', InputArgument::REQUIRED, 'The name of the asset to be edited')
            ->addArgument('name', InputArgument::OPTIONAL, 'The new name of the asset')
            ->addOption('asset.accounting', null, InputOption::VALUE_OPTIONAL, 'The name of the accounting method to be used', null)
            ->addOption('asset.dateFormat', null, InputOption::VALUE_OPTIONAL, 'The preferred locale for date formats', null)
            ->addOption('asset.moneyFormat', null, InputOption::VALUE_OPTIONAL, 'The preferred locale for monetary formats', null)
            ->addOption('asset.moneyCurrency', null, InputOption::VALUE_OPTIONAL, 'The currency of the monetary values', null)
            ->addOption('asset.moneyScale', null, InputOption::VALUE_OPTIONAL, 'The number of decimal places to keep in monetary values', null)
            ->addOption('asset.moneyRounding', null, InputOption::VALUE_OPTIONAL, 'The rounding mode to apply in monetary calculations', null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $asset = $this->getAsset($input, $output, 'asset');
        if (!$asset) return Command::FAILURE;

        $account = $input->getOption('asset.accounting') 
            ? $this->getAccount($input, $output, 'asset.accounting')
            : $asset->getAccount()
            ;

        if (!$account) return Command::FAILURE;

        $rounding = $input->getOption('asset.moneyRounding')
            ? $this->getRounding($input, $output, 'asset.moneyRounding')
            : $asset->getMoneyRounding()
            ;

        if (!$rounding) return Command::FAILURE;

        $asset
            ->setName($input->getArgument('name') ?? $asset->getName())
            ->setAccount($account)
            ->setDateFormat($input->getOption('asset.dateFormat') ?? $asset->getDateFormat())
            ->setMoneyFormat($input->getOption('asset.moneyFormat') ?? $asset->getMoneyFormat())
            ->setMoneyCurrency($input->getOption('asset.moneyCurrency') ?? $asset->getMoneyCurrency())
            ->setMoneyScale($input->getOption('asset.moneyScale') ?? $asset->getMoneyScale())
            ->setMoneyRounding($rounding)
            ;

        $errors = $this->validator->validate($asset);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $io->error([$error->getPropertyPath(), sprintf($error->getMessage(), $input->getArgument('name'))]);
            }

            return Command::FAILURE;
        }
        
        $this->entityManager->persist($asset);
        $this->entityManager->flush();

        $io->success(sprintf(Asset::MESSAGE_SUCCESS_UPDATED, $asset->getName()));

        return Command::SUCCESS;
    }
}
