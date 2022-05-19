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
    name: 'app:asset:add|add',
    description: 'Create a new Asset',
)]
class AssetAddCommand extends StackaCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the asset')
            ->addOption('asset.accounting', 'asset.a', InputOption::VALUE_OPTIONAL, 'The preferred accounting name', 'average')
            ->addOption('asset.dateFormat', 'asset.dF', InputOption::VALUE_OPTIONAL, 'The preferred locale for date formats', 'en')
            ->addOption('asset.moneyFormat', 'asset.mF', InputOption::VALUE_OPTIONAL, 'The preferred locale for monetary formats', 'en')
            ->addOption('asset.moneyCurrency', 'asset.mC', InputOption::VALUE_OPTIONAL, 'The currency of the monetary values', 'USD')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $account = $this->getAccount($input, $output, 'asset.accounting');
        if (!$account) return Command::FAILURE;
        
        $asset = new Asset();
        $asset
            ->setName($input->getArgument('name'))
            ->setAccount($account)
            ->setDateFormat($input->getOption('asset.dateFormat'))
            ->setMoneyFormat($input->getOption('asset.moneyFormat'))
            ->setMoneyCurrency($input->getOption('asset.moneyCurrency'))
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

        $io->success(sprintf(Asset::MESSAGE_SUCCESS_CREATED, $asset->getName()));

        return Command::SUCCESS;
    }
}
