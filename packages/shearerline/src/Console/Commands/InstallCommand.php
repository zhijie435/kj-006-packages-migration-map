<?php

namespace Shearerline\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'shearerline:install';

    protected $description = 'Install the Shearerline package (publish config, migrations, seeds, assets).';

    public function handle(): int
    {
        $this->info('Installing Shearerline...');

        $tags = [
            'shearerline-config',
            'shearerline-migrations',
            'shearerline-seeds',
            'shearerline-assets',
            'shearerline-views',
            'shearerline-lang',
        ];

        foreach ($tags as $tag) {
            $this->call('vendor:publish', ['--tag' => $tag]);
        }

        if ($this->confirm('Run migrations now?')) {
            $this->call('migrate');
        }

        $this->info('Shearerline installed successfully.');

        return self::SUCCESS;
    }
}
