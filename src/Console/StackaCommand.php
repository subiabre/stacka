<?php

namespace App\Console;

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

    public function __construct(
        AssetRepository $assetRepository,
        TransactionRepository $transactionRepository,
        ValidatorInterface $validatorInterface,
        EntityManagerInterface $entityManagerInterface,
    ) {
        parent::__construct();
        
        $this->assetRepository = $assetRepository;
        $this->transactionRepository = $transactionRepository;
        $this->validator = $validatorInterface;
        $this->entityManager = $entityManagerInterface;
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
}
