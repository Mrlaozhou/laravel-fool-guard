## Install
```php
composer require mrlaozhou/laravel-fool-guard
```

## config
更改config/auth.php
```php
'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'fool',
            'provider' => 'users',
        ],
    ],
```
## Commands
```php
php artisan fool-guard:migrate 
php artisan fool-guard:rollback
//  清除过期token
php artisan fool-guard:clearStale 
```