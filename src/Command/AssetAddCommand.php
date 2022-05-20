<?php

namespace App\Command;

use App\Accounting\Balance\Rounding;
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
            ->addOption('asset.accounting', null, InputOption::VALUE_OPTIONAL, 'The preferred accounting name', 'average')
            ->addOption('asset.dateFormat', null, InputOption::VALUE_OPTIONAL, 'The preferred locale for date formats', 'en')
            ->addOption('asset.moneyFormat', null, InputOption::VALUE_OPTIONAL, 'The preferred locale for monetary formats', 'en')
            ->addOption('asset.moneyCurrency', null, InputOption::VALUE_OPTIONAL, 'The currency of the monetary values', 'USD')
            ->addOption('asset.moneyScale', null, InputOption::VALUE_OPTIONAL, 'The number of zeroes to keep in monetary values', 2)
            ->addOption('asset.moneyRounding', null, InputOption::VALUE_OPTIONAL, 'The rounding mode to apply in monetary calculations', 'half-even')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $account = $this->getAccount($input, $output, 'asset.accounting');
        if (!$account) return Command::FAILURE;

        $rounding = $this->getRounding($input, $output, 'asset.moneyRounding');
        if (!$rounding) return Command::FAILURE;
        
        $asset = new Asset();
        $asset
            ->setName($input->getArgument('name'))
            ->setAccount($account)
            ->setDateFormat($input->getOption('asset.dateFormat'))
            ->setMoneyFormat($input->getOption('asset.moneyFormat'))
            ->setMoneyCurrency($input->getOption('asset.moneyCurrency'))
            ->setMoneyScale($input->getOption('asset.moneyScale'))
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

        $io->success(sprintf(Asset::MESSAGE_SUCCESS_CREATED, $asset->getName()));

        return Command::SUCCESS;
    }
}
