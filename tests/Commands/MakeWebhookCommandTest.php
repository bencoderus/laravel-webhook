<?php

namespace Bencoderus\Webhook\Tests\Commands;

use Bencoderus\Webhook\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MakeWebhookCommandTest extends TestCase
{
    public function testWebhookFileIsCreatedThroughACommand()
    {
        $fileName = "UserWebhook";
        $filePath = app_path("Http/Webhooks/{$fileName}.php");

        if (File::exists($filePath)) {
            unlink($filePath);
        }

        $this->assertFalse(File::exists($filePath));

        Artisan::call("make:webhook {$fileName}");

        $this->assertTrue(File::exists($filePath));
    }
}
