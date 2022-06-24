<?php

namespace App\Command;

use App\Accounting\Balance\Balance;
use App\Accounting\Transaction\TransactionType;
use App\Console\StackaCommand;
use App\Entity\Asset;
use App\Entity\Transaction;
use Brick\Math\BigRational;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:asset:import|import',
    description: 'Import assets from a JSON file',
)]
class AssetImportCommand extends StackaCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'The name of the file where data was exported')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $file = sprintf("%s.json", rtrim($input->getArgument('file'), '.json'));
        foreach (json_decode(file_get_contents($file), true) as $data) {
            $account = $this->accountLocator->filterByName($data['account']);
            if (!$account) return Command::FAILURE;

            $rounding = $this->roundingLocator->filterByName($data['moneyRounding']);
            if (!$rounding) return Command::FAILURE;
            
            $asset = new Asset();
            $asset
                ->setName($data['name'])
                ->setAccount($account)
                ->setDateFormat($data['dateFormat'])
                ->setMoneyFormat($data['moneyFormat'])
                ->setMoneyCurrency($data['moneyCurrency'])
                ->setMoneyScale($data['moneyScale'])
                ->setMoneyRounding($rounding)
                ->setTransactions(new ArrayCollection(array_map(function($trxData) use ($asset) {
                    $transaction = new Transaction();
                    $transaction
                        ->setAsset($asset)
                        ->setDate(new \DateTime($trxData['date']['date'], new \DateTimeZone($trxData['date']['timezone'])))
                        ->setType(TransactionType::from($trxData['type']))
                        ->setBalance(new Balance(
                            BigRational::of($trxData['balance']['amount']),
                            BigRational::of($trxData['balance']['money'])
                            )
                        );

                    return $transaction;
                }, $data['transactions'])))
                ;

            $errors = $this->validator->validate($asset);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $io->error([$error->getPropertyPath(), sprintf($error->getMessage(), $data['name'])]);
                }

                return Command::FAILURE;
            }

            $this->entityManager->persist($asset);
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
