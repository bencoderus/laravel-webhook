<?php

namespace Bencoderus\Webhook;

use Bencoderus\Webhook\Console\InstallCommand;
use Bencoderus\Webhook\Console\MakeWebhookCommand;
use Illuminate\Support\ServiceProvider;

class WebhookServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('webhook.php'),
            ], 'config');

            if (! class_exists('CreateWebhookLogsTable')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_webhook_logs_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_webhook_logs_table.php'),
                    // you can add any number of migrations here
                ], 'migrations');
            }

            // Registering package commands.
            $this->commands([MakeWebhookCommand::class, InstallCommand::class]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'webhook');

    }
}
