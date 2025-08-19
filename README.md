<p align="center"><a href="https://codenteq.com" target="_blank"><img src="src/Resources/assets/images/yurtici.svg" width="288"></a></p>

# Yurtiçi Shipping
[![License](https://poser.pugx.org/codenteq/yurtici-shipping/license)](https://github.com/codenteq/yurtici-shipping/blob/master/LICENSE)
[![Total Downloads](https://poser.pugx.org/codenteq/yurtici-shipping/d/total)](https://packagist.org/packages/codenteq/yurtici-shipping)

## 1. Introduction:

Yurtiçi Shipping Add-on provides Yurtiçi Shipping methods for shipping the product.

## 2. Requirements:

* **PHP**: 8.2 or higher.
* **Bagisto**: v2.*
* **Composer**: 1.6.5 or higher.

## 3. Installation:

- Run the following command
```
composer require codenteq/yurtici-shipping
```

- Run these commands below to complete the setup
```
composer dump-autoload
```

- Run these commands below to complete the setup
```
php artisan optimize
```

## Installation without composer:

- To ensure that your custom shipping method package is properly integrated into the Bagisto application, you need to register your service provider. This can be done by adding it to the `bootstrap/providers.php` file in the Bagisto root directory.

```
Webkul\YurticiShipping\Providers\YurticiShippingServiceProvider::class,
```

- Goto composer.json file and add following line under 'psr-4'

```
"Webkul\\YurticiShipping\\": "packages/Webkul/YurticiShipping/src"
```

- Run these commands below to complete the setup

```
composer dump-autoload
```

```
php artisan optimize
```

> That's it, now just execute the project on your specified domain.

## How to contribute
Yurtiçi Shipping is always open for direct contributions. Contributions can be in the form of design suggestions, documentation improvements, new component suggestions, code improvements, adding new features or fixing problems. For more information please check our [Contribution Guideline document.](https://github.com/codenteq/yurtici-shipping/blob/master/CONTRIBUTING.md)
