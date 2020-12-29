[![Latest Version on Packagist](https://img.shields.io/packagist/v/yabhq/laravel-cart.svg?style=flat-square)](https://packagist.org/packages/yabhq/laravel-cart)
[![CircleCI](https://circleci.com/gh/yabhq/laravel-cart.svg?style=svg)](https://circleci.com/gh/yabhq/laravel-cart)

# Laravel Shopping Cart API

A simple yet customizable Laravel shopping cart API. Provides RESTful API endpoints out of the box to help with your next e-commerce build. Designed specifically with single page application (SPA) requirements in mind. Currently supports payment processing with **Stripe** out of the box.

### Table of Contents
[Requirements](#requirements)
[Installation](#installation)
[Configuration](#configuration)
[Usage](#usage)
[Advanced Usage: Checkout Class](#the-checkout-class)
[License](#license)

### Requirements

* PHP 8+
* Laravel 8.x

### Installation

```
composer require yabhq/laravel-cart
```

### Configuration

The package publishes some migrations, routes (for optional use) and classes for further customizing your store logistics. 

```
php artisan vendor:publish --provider="Yab\ShoppingCart\ShoppingCartServiceProvider"
```

Full list of published files:

* database/migrations/2020_12_13_000001_create_carts_table
* database/migrations/2020_12_13_000002_create_cart_items_table
* routes/checkout.php
* config/checkout.php
* app/Logistics/CartLogistics.php
* app/Logistics/ShippingLogistics.php
* app/Logistics/TaxLogistics.php

### Usage

First, simply implement the *Purchaseable* interface on your product (or other purchaseable) model.

**app/Models/Product.php**
```php
use Yab\ShoppingCart\Traits\Purchaseable;
use Yab\ShoppingCart\Contracts\Purchaseable as PurchaseableInterface;

class Product extends Model implements PurchaseableInterface
{
    use Purchaseable;
}
```

If you would like to use the built-in cart API endpoints, you can simply include *checkout.php* in your existing routes file.

**routes/api.php**
```php
Route::group(['middleware' => ['example']], function () {
    require base_path('routes/checkout.php');
});
```

This will add the following routes:

```
POST /checkouts
GET /checkouts/{checkout}
PUT /checkouts/{checkout}
DELETE /checkouts/{checkout}

POST /checkouts/{checkout}/items
PUT /checkouts/{checkout}/items/{item}
DELETE /checkouts/{checkout}/items/{item}
```

Not every e-commerce store is the same. This package provides several "logistics" classes which allow you to hook into the core package logic and perform some common customizations. For example, you may specify how the tax & shipping costs are determined:

**app/Logistics/TaxLogistics.php**
```php
public static function getTaxes(float $subtotal, float $shipping, Cart $cart) : float
```

**app/Logistics/ShippingLogistics.php**
```php
public static function getShippingCost(Cart $cart) : float
```

### The Checkout Class

For more advanced usage, the package comes with a *Checkout* class which allows you to interact with the shopping cart directly. This may be useful in case you want to implement your own custom controller logic.

Creating or retrieving a checkout instance:

```php
$checkout = Checkout::create();
// or
$checkout = Checkout::findById('uuid-123');
```

Adding a custom field for a checkout:

```php
$checkout->setCustomField('some key', 'some value');
```

Deleting a checkout:

```php
$checkout->destroy();
```

Interacting with the underlying cart models and query builder:

```php
// Yab\ShoppingCart\Models\Cart
$checkout->getCart();

// Illuminate\Database\Eloquent\Builder
$checkout->getCartBuilder();
```

Adding, updating or removing cart items:

```php
// Add 1 qty of product and return the CartItem model
$item = $checkout->addItem($product, 1);

// Override the default unit price for the product
$item = $checkout->addItem($product, 1, 11.95);

// Add custom options to a checkout item
$item = $checkout->addItem(
    purchaseable: $product,
    qty: 1,
    options: [ 'size' => 'medium' ],
);

// Update the quantity of the item to 2
$checkout->updateItem($item->id, 2);

// Remove the item entirely
$checkout->removeItem($item->id);
```

Getting the shipping, subtotal, taxes and total:

```php
$checkout->getShipping(); // 5.00
$checkout->getSubtotal(); // 100.00
$checkout->getTaxes(); // 13.00
$checkout->getTotal(); // 113.00
```

Performing charges (currently supports Stripe):

```php
$checkout->setPaymentProvider('stripe')->charge([ 'token' => ... ]);
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.