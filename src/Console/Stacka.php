<?php

namespace App\Console;

use SebastianBergmann\Version;
use Symfony\Component\Console\Application;

class Stacka extends Application
{
    public function __construct(string $path, iterable $commands)
    {
        parent::__construct('Stacka', (new Version('0.1.0', $path))->getVersion());

        foreach ($commands as $command) {
            $this->add($command);
        };
    }
}
