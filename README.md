# Real-Time monitoring package using Palzin Monitor

[![Latest Stable Version](http://poser.pugx.org/palzin-apm/palzin-laravel/v?style=for-the-badge)](https://packagist.org/packages/palzin-apm/palzin-laravel) [![Total Downloads](http://poser.pugx.org/palzin-apm/palzin-laravel/downloads?style=for-the-badge)](https://packagist.org/packages/palzin-apm/palzin-laravel) [![License](http://poser.pugx.org/palzin-apm/palzin-laravel/license?style=for-the-badge)](https://packagist.org/packages/palzin-apm/palzin-laravel)

Palzin Monitor offers real-time performance monitoring capabilities that allow you to effectively monitor and analyze the performance of your applications. With Palzin Monitor, you can capture and track all requests without the need for any code modifications. This feature enables you to gain valuable insights into the impact of your methods, database statements, and external requests on the overall user experience.


- [Requirements](#requirements)
- [Installation](#installation)
- [Configure the Ingestion Key](#key)
- [Middleware Setup](#middleware)
- [Test everything is working](#test)

<a name="requirements"></a>

## Requirements

- PHP >= 7.2.0
- Laravel >= 5.5

<a name="install"></a>

## Install



To install the latest version of Palzin Monitor (APM) use below command:

```
composer require palzin-apm/palzin-laravel
```

## For Lumen
If your application is based on Lumen you need to manually register the `PalzinServiceProvider`:

```php
$app->register(\Palzin\Laravel\PalzinServiceProvider::class);
```


<a name="key"></a>

### Configure the Ingestion Key

First put the Ingestion Key in your environment file:

```
PALZIN_APM_INGESTION_KEY=[your ingestion key]
```

You can obtain an `PALZIN_APM_INGESTION_KEY` creating a new project in your [Palzin APM](https://www.palzin.app) account.

<a name="middleware"></a>

### Attach the Middleware

To monitor web requests you can attach the `WebMonitoringMiddleware` in your http kernel or use in one or more route groups based on your personal needs.

```php
/**
 * The application's route middleware groups.
 *
 * @var array
 */
protected $middlewareGroups = [
    'web' => [
        ...,
        \Palzin\Laravel\Middleware\WebRequestMonitoring::class,
    ],

    'api' => [
        ...,
        \Palzin\Laravel\Middleware\WebRequestMonitoring::class,
    ]
```

<a name="test"></a>

### Test everything is working

Run the command below:

```
php artisan palzin:test
```

Go to [https://www.palzin.app/](https://www.palzin.app/) to explore your data.

## LICENSE

This package is licensed under the [MIT](LICENSE) license.
