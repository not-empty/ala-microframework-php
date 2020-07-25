<?php

namespace App\Console;

use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        'App\Console\Commands\CreateDomainCommand',
        'App\Console\Commands\MigrateTemplateCommand',
        'App\Console\Commands\RandomSeedIndexCommand',
    ];
}
