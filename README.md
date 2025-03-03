# Zip SDK for Laravel

A Laravel SDK for integrating with Zip payment services.

> **New Feature**: Payment Links API now available! See [Payment Links](#payment-links) section below.

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

### Listing Checkout Sessions

```php
use Domdanao\ZipSdkLaravel\Facades\ZipCheckoutService;

// List all sessions
$sessions = ZipCheckoutService::listSessions();

// List sessions with optional parameters
$sessions = ZipCheckoutService::listSessions([
    'limit' => 10,
    'starting_after' => 'cs_123456',
]);

foreach ($sessions['data'] as $session) {
    echo "Session ID: {$session->id}, Status: {$session->status}\n";
}
```

### Capturing a Checkout Session

```php
use Domdanao\ZipSdkLaravel\Facades\ZipCheckoutService;

// Capture the full amount
$session = ZipCheckoutService::captureSession('cs_123456');

// Capture a partial amount
$session = ZipCheckoutService::captureSession('cs_123456', [
    'amount' => 5000, // 50.00 in cents
]);
```

### Expiring a Checkout Session

```php
use Domdanao\ZipSdkLaravel\Facades\ZipCheckoutService;

$session = ZipCheckoutService::expireSession('cs_123456');

if ($session->status === 'expired') {
    // Session was successfully expired
}
```

### Canceling a Checkout Session

```php
use Domdanao\ZipSdkLaravel\Facades\ZipCheckoutService;

$session = ZipCheckoutService::cancelSession('cs_123456');

if ($session->status === 'canceled') {
    // Session was successfully canceled
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

### Retrieving a Payment Source

```php
use Domdanao\ZipSdkLaravel\Facades\ZipService;

$source = ZipService::getSource('src_123456');
```

### Attaching a Source to a Customer

```php
use Domdanao\ZipSdkLaravel\Facades\ZipService;

$source = ZipService::attachSource('cus_123456', 'src_789012');
```

### Detaching a Source from a Customer

```php
use Domdanao\ZipSdkLaravel\Facades\ZipService;

$result = ZipService::detachSource('cus_123456', 'src_789012');
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

### Retrieving a Charge

```php
use Domdanao\ZipSdkLaravel\Facades\ZipService;

$charge = ZipService::getCharge('ch_123456');
```

### Listing Charges

```php
use Domdanao\ZipSdkLaravel\Facades\ZipService;

// List all charges
$charges = ZipService::listCharges();

// List charges with optional parameters
$charges = ZipService::listCharges([
    'limit' => 10,
    'starting_after' => 'ch_123456',
]);
```

### Capturing a Charge

```php
use Domdanao\ZipSdkLaravel\Facades\ZipService;

$charge = ZipService::captureCharge('ch_123456');
```

### Refunding a Charge

```php
use Domdanao\ZipSdkLaravel\Facades\ZipService;

$charge = ZipService::refundCharge('ch_123456');

// Partial refund
$charge = ZipService::refundCharge('ch_123456', [
    'amount' => 5000, // 50.00 in cents
]);
```

### Voiding a Charge

```php
use Domdanao\ZipSdkLaravel\Facades\ZipService;

$charge = ZipService::voidCharge('ch_123456');
```

### Verifying a Charge

```php
use Domdanao\ZipSdkLaravel\Facades\ZipService;

$charge = ZipService::verifyCharge('ch_123456');
```

## Payment Requests

Payment Requests allow you to send payment links to customers via email.

### Creating a Payment Request

```php
use Domdanao\ZipSdkLaravel\Facades\PaymentRequestService;

$request = PaymentRequestService::createRequest([
    'amount' => 10000, // 100.00 in cents
    'currency' => 'PHP',
    'description' => 'Payment for Invoice #12345',
    'customer_email' => 'customer@example.com',
    'success_url' => url('/payment/success'),
    'cancel_url' => url('/payment/cancel'),
    'metadata' => [
        'invoice_id' => '12345',
    ],
    'line_items' => [
        [
            'name' => 'Product Name',
            'amount' => 10000,
            'quantity' => 1,
            'description' => 'Product description',
        ],
    ],
    'expires_at' => now()->addDays(7)->toIso8601String(),
]);

// Get the payment URL
$paymentUrl = $request->paymentUrl;
```

### Retrieving a Payment Request

```php
use Domdanao\ZipSdkLaravel\Facades\PaymentRequestService;

$request = PaymentRequestService::getRequest('req_123456');

// Check request status
if ($request->isPaid()) {
    // Payment was successful
} elseif ($request->isPending()) {
    // Payment is still pending
} elseif ($request->isExpired()) {
    // Payment request has expired
} elseif ($request->isCanceled()) {
    // Payment request was canceled
}
```

### Listing Payment Requests

```php
use Domdanao\ZipSdkLaravel\Facades\PaymentRequestService;

// List all payment requests
$requests = PaymentRequestService::listRequests();

// List payment requests with optional parameters
$requests = PaymentRequestService::listRequests([
    'limit' => 10,
    'starting_after' => 'req_123456',
]);

foreach ($requests['data'] as $request) {
    echo "Request ID: {$request->id}, Status: {$request->status}\n";
}
```

### Resending a Payment Request

```php
use Domdanao\ZipSdkLaravel\Facades\PaymentRequestService;

$request = PaymentRequestService::resendRequest('req_123456');
```

### Voiding a Payment Request

```php
use Domdanao\ZipSdkLaravel\Facades\PaymentRequestService;

$request = PaymentRequestService::voidRequest('req_123456');

if ($request->isCanceled()) {
    // Request was successfully voided
}
```

## Payment Links

Payment Links allow you to create shareable payment links that can be used multiple times.

### Creating a Payment Link

```php
use Domdanao\ZipSdkLaravel\Facades\PaymentLinkService;

$link = PaymentLinkService::createLink([
    'name' => 'Product Name',
    'description' => 'Product description',
    'amount' => 10000, // 100.00 in cents
    'currency' => 'PHP',
    'success_url' => url('/payment/success'),
    'cancel_url' => url('/payment/cancel'),
    'metadata' => [
        'product_id' => '12345',
    ],
    'line_items' => [
        [
            'name' => 'Product Name',
            'amount' => 10000,
            'quantity' => 1,
            'description' => 'Product description',
        ],
    ],
    'active' => true,
    'expires_at' => now()->addMonths(3)->toIso8601String(),
]);

// Get the payment URL
$paymentUrl = $link->url;
```

### Retrieving a Payment Link

```php
use Domdanao\ZipSdkLaravel\Facades\PaymentLinkService;

$link = PaymentLinkService::getLink('link_123456');

// Check link status
if ($link->isActive()) {
    // Link is active
} elseif ($link->isExpired()) {
    // Link has expired
}
```

### Listing Payment Links

```php
use Domdanao\ZipSdkLaravel\Facades\PaymentLinkService;

// List all payment links
$links = PaymentLinkService::listLinks();

// List active payment links
$links = PaymentLinkService::listLinks([
    'active' => true,
]);

// List payment links with pagination
$links = PaymentLinkService::listLinks([
    'limit' => 10,
    'starting_after' => 'link_123456',
]);

foreach ($links['data'] as $link) {
    echo "Link ID: {$link->id}, Name: {$link->name}, URL: {$link->url}\n";
}
```

### Updating a Payment Link

```php
use Domdanao\ZipSdkLaravel\Facades\PaymentLinkService;

$link = PaymentLinkService::updateLink('link_123456', [
    'name' => 'Updated Product Name',
    'description' => 'Updated product description',
    'amount' => 15000, // 150.00 in cents
]);
```

### Deactivating a Payment Link

```php
use Domdanao\ZipSdkLaravel\Facades\PaymentLinkService;

$link = PaymentLinkService::deactivateLink('link_123456');

if (!$link->isActive()) {
    // Link was successfully deactivated
}
```

### Activating a Payment Link

```php
use Domdanao\ZipSdkLaravel\Facades\PaymentLinkService;

$link = PaymentLinkService::activateLink('link_123456');

if ($link->isActive()) {
    // Link was successfully activated
}
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
