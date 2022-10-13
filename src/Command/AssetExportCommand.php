<?php

namespace App\Command;

use App\Console\StackaCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:asset:export|export',
    description: 'Export a list of Assets to JSON',
)]
class AssetExportCommand extends StackaCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('assets', InputArgument::IS_ARRAY, 'The name of the assets to be imported', ['%%'])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $assets = [];
        foreach ($input->getArgument('assets') as $name) {
            $assets = [...$assets, ...$this->assetRepository->findLikeName($name)];
        }
        
        $json = [];
        foreach ($assets as $asset) {
            array_push($json, [
                'name' => $asset->getName(),
                'account' => $asset->getAccount()->getName(),
                'dateFormat' => $asset->getDateFormat(),
                'moneyFormat' => $asset->getMoneyFormat(),
                'moneyCurrency' => $asset->getMoneyCurrency(),
                'moneyScale' => $asset->getMoneyScale(),
                'moneyRounding' => $asset->getMoneyRounding()->getName(),
                'transactions' => array_map(function($transaction) {
                    return [
                        'date' => $transaction->getDate(),
                        'type' => $transaction->getType(),
                        'balance' => [
                            'amount' => $transaction->getBalance()->getAmount(),
                            'money' => $transaction->getBalance()->getMoney(),
                        ]
                    ];
                }, $asset->getTransactions()->toArray())
            ]);
        }

        $output->write(json_encode($json));

        return Command::SUCCESS;
    }
}
