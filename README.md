# Laravel Webhook

[![Latest Stable Version](https://poser.pugx.org/bencoderus/laravel-webhook/v)](//packagist.org/packages/bencoderus/laravel-webhook)
[![Total Downloads](https://poser.pugx.org/bencoderus/laravel-webhook/downloads)](//packagist.org/packages/bencoderus/laravel-webhook)
[![License](https://poser.pugx.org/bencoderus/laravel-webhook/license)](//packagist.org/packages/bencoderus/laravel-webhook)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bencoderus/laravel-webhook/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bencoderus/laravel-webhook/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/bencoderus/laravel-webhook/badges/build.png?b=master)](https://scrutinizer-ci.com/g/bencoderus/laravel-webhook/build-status/master)

Laravel webhook allows businesses to send webhooks to their merchants/clients with ease. This package also introduces a
new artisan command to generate a webhook class.

## Requirement

- Composer v1/v2
- Php >= 7.3
- Laravel (6 and above).

## Installation

You can install the package via composer:

```bash
composer require bencoderus/laravel-webhook
```

## Setup

Publish basic components. (migrations and configuration files)

``` php
php artisan webhook:install
```

Run migrations

```bash
php artisan migrate
```

## Basic usage

Create a new webhook class

```bash
php artisan make:webhook PaymentWebhook
```

Creates a new webhook class in App\Http\Webhooks

You can format your webhook payload like a resource.

```php
public function data(): array
    {
        return [
            'status' => $this->status,
            'amount' => $this->amount,
            'currency' => 'USD',
        ];
    }
```

<br/>
Sending a webhook.

```php
$transaction = Transaction::first();

$webhook = new PaymentWebhook($transaction);
$webhook->url('https://httpbin.com')->send();
```

Sending with an encrypted signature

```php
$transaction = Transaction::first();

$webhook = new PaymentWebhook($transaction);
$webhook->url('https://httpbin.com')
        ->withSignature('x-key', 'value_to_hash')
        ->send();
````

The default hashing algorithm is sha512 you can change it by passing a different hashing algorithm as the third
parameter for the withSignature method. PHP currently supports over 50 hashing algorithms.

Sending webhooks without using a Queue.
<br/>
By default, all webhooks are dispatched using a queue to facilitate webhook retrial after failure. You can also send
webhooks without using a Queue by passing ``false``  to the send method.

```php
$transaction = Transaction::first();

$webhook = new PaymentWebhook($transaction);
$webhook->url('https://httpbin.com')->send(false);
```

### Testing

``` bash
composer test
```

## Configuration

- You can enable or disable sending webhook via config/webhook.php.
- You can also enable or disable logging webhook via config/webhook.php and more.

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email me@biduwe.com instead of using the issue tracker.

## Credits

- [Benjamin Iduwe](https://github.com/bencoderus)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
