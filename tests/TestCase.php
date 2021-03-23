<?php

namespace Bencoderus\Webhook\Tests;

use Bencoderus\Webhook\WebhookServiceProvider;
use Orchestra\Testbench\TestCase as MainCase;

class TestCase extends MainCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [WebhookServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $this->runMigrations();
    }

    private function runMigrations()
    {
        // import the CreatePostsTable class from the migration
        include_once __DIR__ . '/../database/migrations/create_webhook_logs_table.php.stub';

        // run the up() method of that migration class
        (new \CreateWebhookLogsTable)->up();
    }
}
