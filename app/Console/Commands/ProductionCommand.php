<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that only runs in production
 */
abstract class ProductionCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        // Check environment
        if (!App::environment('production') || Config::get('app.beta')) {
            $this->alert('This command is not available in beta mode');
            $this->line(<<<'TEXT'
            This command probably uses sensitive data, and is
            only available in production environments that are
            not in beta.
            TEXT);
            $this->error('Command aborted');
            exit(1);
        };

        // Forward
        parent::initialize($input, $output);
    }
}
