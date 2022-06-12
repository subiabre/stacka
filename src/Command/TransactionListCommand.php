<?php

namespace App\Command;

use App\Console\StackaCommand;
use App\Entity\Asset;
use App\Entity\Transaction;
use App\Service\AssetFormatterService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:trx:list|read',
    description: 'List the Transactions from a number of Assets',
)]
class TransactionListCommand extends StackaCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('assets', InputArgument::IS_ARRAY, 'The name of the assets to be read')
            ->addOption('table.offset', null, InputOption::VALUE_OPTIONAL, 'Start the list at this item number', 0)
            ->addOption('table.limit', null, InputOption::VALUE_OPTIONAL, 'End the list at this many items after the offset', 10)
            ->addOption('asset.accounting', null, InputOption::VALUE_OPTIONAL, 'The name of the accounting method to be used', null)
            ->addOption('asset.dateFormat', null, InputOption::VALUE_OPTIONAL, 'The preferred locale for date formats', null)
            ->addOption('asset.moneyFormat', null, InputOption::VALUE_OPTIONAL, 'The preferred locale for monetary formats', null)
            ->addOption('asset.moneyCurrency', null, InputOption::VALUE_OPTIONAL, 'The currency of the monetary values', null)
            ->addOption('asset.moneyScale', null, InputOption::VALUE_OPTIONAL, 'The number of decimal places to keep in monetary values', null)
            ->addOption('asset.moneyRounding', null, InputOption::VALUE_OPTIONAL, 'The rounding mode to apply in monetary calculations', null)
            ->setHelp(<<<'EOF'
            The <info>%command.name%</info> command reads the transactions in a list of assets.
            It's possible to apply the <comment>%</comment> wildcard on every item in the list of assets to read: <info>read chairs_garden chairs_% %_garden</info>

            The first matching asset in the list is taken as sample and the transactions in the rest of the assets are added to that one.
            You can also specify asset properties on-the-fly by using the <comment>--asset.*</comment> options.
            EOF
                    )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $names = $input->getArgument('assets');
        if (empty($names)) {
            $io->error("You must provide at least one asset name.");
            return Command::FAILURE;
        }

        $assets = [];
        foreach ($names as $name) {
            $assets = [...$assets, ...$this->assetRepository->findLikeName($name)];
        }

        if (empty($assets)) {
            $io->error(array_map(function ($name) { return sprintf("Could not match '%s' to any asset in the database.", $name); }, $names));
            return Command::FAILURE;
        }

        $merge = $assets[0];
        $transactions = $this->transactionRepository->findBy(
            ['asset' => array_map(function($asset) { return $asset->getId(); }, $assets)],
            ['date' => 'ASC']
        );

        $account = $input->getOption('asset.accounting') 
            ? $this->getAccount($input, $output, 'asset.accounting')
            : $merge->getAccount()
            ;

        if (!$account) return Command::FAILURE;

        $rounding = $input->getOption('asset.moneyRounding')
            ? $this->getRounding($input, $output, 'asset.moneyRounding')
            : $merge->getMoneyRounding()
            ;

        if (!$rounding) return Command::FAILURE;

        $merge
            ->setTransactions(new ArrayCollection($transactions))
            ->setAccount($account)
            ->setDateFormat($input->getOption('asset.dateFormat') ?? $merge->getDateFormat())
            ->setMoneyFormat($input->getOption('asset.moneyFormat') ?? $merge->getMoneyFormat())
            ->setMoneyCurrency($input->getOption('asset.moneyCurrency') ?? $merge->getMoneyCurrency())
            ->setMoneyScale($input->getOption('asset.moneyScale') ?? $merge->getMoneyScale())
            ->setMoneyRounding($rounding)
            ;

        $account = $merge->getAccount();
        $formatter = new AssetFormatterService($merge);

        $transactions = array_map(function(Transaction $transaction) use ($account, $formatter) {
            $account->addTransaction($transaction);

            return [
                $transaction->getAsset()->getTransactions()->indexOf($transaction),
                $transaction->getId(),
                $formatter->date($transaction->getDate()),
                $transaction->getType()->value,
                $formatter->amount($transaction->getBalance()->getAmount()),
                $formatter->money($transaction->getBalance()->getMoney()),
                $formatter->money($transaction->getBalance()->getMoneyAverage()),
                $formatter->amount($account->getInventory()->getAmount()),
                $formatter->money($account->getInventory()->getMoney()),
                $formatter->money($account->getInventory()->getMoneyAverage()),
                $formatter->amount($account->getSales()->getAmount()),
                $formatter->money($account->getSales()->getMoney()),
                $formatter->money($account->getEarnings())
            ];
        }, $merge->getTransactions()->toArray());

        $io->table([
            'T. #',
            'T. ID',
            'T. Date',
            'T. Type',
            'T. Amount',
            'T. Money',
            'T. Average',
            'I. Amount',
            'I. Money',
            'I. Average',
            'S. Amount',
            'S. Money',
            'Earnings'
        ], array_slice($transactions, $input->getOption('table.offset'), $input->getOption('table.limit')));

        return Command::SUCCESS;
    }
}
