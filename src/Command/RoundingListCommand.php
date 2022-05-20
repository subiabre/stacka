<?php

namespace App\Command;

use App\Accounting\Rounding\RoundingInterface;
use App\Console\StackaCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:rounding:list|roundings',
    description: 'List the available rounding modes',
)]
class RoundingListCommand extends StackaCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $roundings = $this->roundingLocator->getRoundings();

        $io->listing(array_map(function(RoundingInterface $rounding) {
            return $rounding::getName();
        }, \iterator_to_array($roundings)));

        return Command::SUCCESS;
    }
}
