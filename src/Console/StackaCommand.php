<?php

namespace App\Console;

use App\Accounting\AbstractAccount;
use App\Accounting\AccountLocator;
use App\Accounting\Rounding\RoundingInterface;
use App\Accounting\Rounding\RoundingLocator;
use App\Accounting\Transaction\TransactionType;
use App\Entity\Asset;
use App\Repository\AssetRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class StackaCommand extends Command
{
    protected AssetRepository $assetRepository;
    protected TransactionRepository $transactionRepository;
    protected ValidatorInterface $validator;
    protected EntityManagerInterface $entityManager;
    protected AccountLocator $accountLocator;
    protected RoundingLocator $roundingLocator;

    public function __construct(
        AssetRepository $assetRepository,
        TransactionRepository $transactionRepository,
        ValidatorInterface $validatorInterface,
        EntityManagerInterface $entityManagerInterface,
        AccountLocator $accountLocator,
        RoundingLocator $roundingLocator
    ) {
        parent::__construct();
        
        $this->assetRepository = $assetRepository;
        $this->transactionRepository = $transactionRepository;
        $this->validator = $validatorInterface;
        $this->entityManager = $entityManagerInterface;
        $this->accountLocator = $accountLocator;
        $this->roundingLocator = $roundingLocator;
    }

    protected function getAsset(InputInterface $input, OutputInterface $output, string $argument): ?Asset
    {
        $io = new SymfonyStyle($input, $output);

        $name = $input->getArgument($argument);
        $asset = $this->assetRepository->findOneByName($name);

        if (!$asset) {  
            $io->error(sprintf(Asset::MESSAGE_ERROR_MISSING, $name));
            return null;
        }

        return $asset;
    }

    protected function getTransactionType(InputInterface $input, OutputInterface $output, string $argument): ?TransactionType
    {
        $io = new SymfonyStyle($input, $output);
        
        $name = $input->getArgument($argument);

        try {
            $type = TransactionType::from($name);
        } catch (\ValueError $e) {
            $io->error(sprintf(TransactionType::MESSAGE_ERROR_UNKNOWN, $name));
            return Command::FAILURE;
        }

        return $type;
    }

    protected function getAccount(InputInterface $input, OutputInterface $output, string $option): ?AbstractAccount
    {
        $io = new SymfonyStyle($input, $output);
        
        $name = $input->getOption($option);
        $account = $this->accountLocator->filterByName($name);

        if (!$account) {
            $io->error(sprintf(AbstractAccount::MESSAGE_ERROR_UNKNOWN, $name));
            return null;
        }

        return $account;
    }

    protected function getRounding(InputInterface $input, OutputInterface $output, string $option): ?RoundingInterface
    {
        $io = new SymfonyStyle($input, $output);
        
        $name = $input->getOption($option);
        $rounding = $this->roundingLocator->filterByName($name);

        if (!$rounding) {
            $io->error(sprintf(RoundingInterface::MESSAGE_ERROR_UNKNOWN, $name));
            return null;
        }

        return $rounding;
    }
}
