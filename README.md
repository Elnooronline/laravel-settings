[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

# Persistent Settings Manager for Laravel

 * Simple key-value storage
 * Support multi-level array (dot delimited keys) structure.
 * Localization supported.
 * Localization using [dimsav/laravel-translatable](https://github.com/dimsav/laravel-translatable)

## Installation

1. Install package

    ```bash
    composer require elnooronline/laravel-settings
    ```

1. Edit config/app.php (Skip this step if you are using laravel 5.5+)

    service provider:

    ```php
    Elnooronline\LaravelSettings\Providers\ServiceProvider::class,
    ```

    class aliases:

    ```php
    'Setting' => Elnooronline\LaravelSettings\Facades\Setting::class,
    ```

1. Create settings table

    ```bash
    php artisan vendor:publish --provider="Elnooronline\LaravelSettings\Providers\ServiceProvider"
    php artisan migrate
    ```

## Usage

```php
Setting::get('name', 'Computer');
// get setting value with key 'name'
// return 'Computer' if the key does not exists

Setting::all();
// get all settings

Setting::lang('en')->get('name', 'Computer');
// get setting value with key and language

Setting::get('name:en', 'Computer');
// get setting value with key and language

Setting::set('name', 'Computer');
// set setting value by key

Setting::lang('en')->set('name', 'Computer');
// set setting value by key and language

Setting::set('name:en', 'Computer');
// set setting value by key and language

Setting::has('name');
// check the key exists, return boolean

Setting::lang('en')->has('name');
// check the key exists by language, return boolean

Setting::has('name:en');
// check the key exists by language, return boolean

Setting::forget('name');
// delete the setting by key

Setting::lang('en')->forget('name');
// delete the setting by key and language

Setting::forget('name:en');
// delete the setting by key and language
```

## Dealing with array

```php
Setting::get('item');
// return null;

Setting::set('item', ['USB' => '8G', 'RAM' => '4G']);
Setting::get('item');
// return array(
//     'USB' => '8G',
//     'RAM' => '4G',
// );

Setting::get('item.USB');
// return '8G';
```

## Conditions
> in your `AppServiceProvider` you can register new prefix method.
```php
public function boot()
{
	Setting::registerPrefixMethod('country');
	...
}
```
```php
Setting::country('us')->set('title', 'Example Website');

Setting::get('name');
// return return 'Example Website';

Setting::country('eg')->set('title', 'عنوان الموقع');

Setting::country('eg')->get('name');
// return return 'عنوان الموقع';

Setting::country('eg')->forget('name');
// delete the setting by key and country
```

[ico-version]: https://img.shields.io/packagist/v/elnooronline/laravel-settings.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

[ico-downloads]: https://img.shields.io/packagist/dt/elnooronline/laravel-settings.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/unisharp/categorizable
[link-travis]: https://travis-ci.org/UniSharp/categorizable
[link-scrutinizer]: https://scrutinizer-ci.com/g/UniSharp/categorizable/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/UniSharp/categorizable
[link-downloads]: https://packagist.org/packages/UniSharp/categorizable
[link-author]: https://github.com/ahmed-aliraqi
