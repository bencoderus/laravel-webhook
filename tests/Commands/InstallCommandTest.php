<?php

namespace Bencoderus\Webhook\Tests\Commands;

use Bencoderus\Webhook\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallCommandTest extends TestCase
{
    public function testCommandInstalledAllTheNecessaryFiles(): void
    {
        if (File::exists(config_path('webhook.php'))) {
            unlink(config_path('webhook.php'));
        }

        $this->assertFalse(File::exists(config_path('webhook.php')));

        Artisan::call('webhook:setup');

        $this->assertTrue(File::exists(config_path('webhook.php')));
        $this->assertTrue(class_exists('CreateWebhookLogsTable'));
    }
}
