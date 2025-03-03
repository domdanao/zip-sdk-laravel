# Zip SDK for Laravel

A Laravel SDK for integrating with Zip payment services.

## Installation

You can install the package via composer:

```bash
composer require domdanao/zip-sdk-laravel
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=zip-sdk-config
```

This will create a `config/zip-sdk.php` file in your app. You should set your Zip API credentials in your `.env` file:

```
ZIP_API_SERVER=https://api.zip.ph
ZIP_PUBLIC_KEY=your_public_key
ZIP_SECRET_KEY=your_secret_key
ZIP_API_VERSION=v2
ZIP_CONVENIENCE_FEE=1500
```

## Usage

### Creating a Checkout Session

```php
use Domdanao\ZipSdkLaravel\Facades\ZipCheckoutService;

// Create a checkout session
$session = ZipCheckoutService::createSession([
    'currency' => 'PHP',
    'payment_method_types' => ['card', 'gcash', 'maya'],
    'success_url' => url("/payment/success"),
    'cancel_url' => url("/payment/cancel"),
    'description' => "Payment for Order #12345",
    'line_items' => [
        [
            'name' => "Product Name",
            'amount' => 10000, // 100.00 in cents
            'currency' => 'PHP',
            'quantity' => 1,
            'description' => "Product description",
        ],
    ],
    'metadata' => [
        'order_id' => '12345',
    ],
]);

// Redirect to payment page
return redirect($session->paymentUrl);
```

### Retrieving a Checkout Session

```php
use Domdanao\ZipSdkLaravel\Facades\ZipCheckoutService;

$session = ZipCheckoutService::getSession('cs_123456');

// Check session status
if ($session->status === 'completed') {
    // Payment was successful
}
```

### Creating a Customer

```php
use Domdanao\ZipSdkLaravel\Facades\ZipService;

$customer = ZipService::createCustomer([
    'email' => 'customer@example.com',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'phone' => '+639123456789',
]);
```

### Creating a Payment Source

```php
use Domdanao\ZipSdkLaravel\Facades\ZipService;

$source = ZipService::createSource([
    'type' => 'card',
    'customer_id' => 'cus_123456',
    'token' => 'tok_123456',
]);
```

### Creating a Charge

```php
use Domdanao\ZipSdkLaravel\Facades\ZipService;

$charge = ZipService::createCharge([
    'amount' => 10000, // 100.00 in cents
    'currency' => 'PHP',
    'customer_id' => 'cus_123456',
    'source_id' => 'src_123456',
    'description' => 'Payment for Order #12345',
    'metadata' => [
        'order_id' => '12345',
    ],
]);
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
