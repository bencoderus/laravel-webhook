<?php

namespace Bencoderus\Webhook\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'webhook:setup';

    protected $description = 'Install Webhook Dependencies';

    public function handle()
    {
        $this->info('Installing the webhook service');

        $this->info('Publishing configuration...');

        $this->call('vendor:publish', [
            '--provider' => "Bencoderus\Webhook\WebhookServiceProvider",
            '--tag' => 'config',
        ]);

        if (! class_exists('CreateWebhookLogsTable')) {
            $this->call('vendor:publish', [
                '--provider' => "Bencoderus\Webhook\WebhookServiceProvider",
                '--tag' => 'migrations',
            ]);
        }

        $this->info('Webhook service installed successfully.');
    }
}
