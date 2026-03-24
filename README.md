# wepaycheckout/laravel

WePay / ClicToPay invoice checkout for Laravel. Installs into `vendor` and auto-registers the service provider.

**Author:** [Dridi Hatem](https://dawebcompany.tn) · [dridihatem@gmail.com](mailto:dridihatem@gmail.com) · [dawebcompany.tn](https://dawebcompany.tn)

## Install from Git (Composer)

In your Laravel app `composer.json`, add the repository and dependency:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/dridihatem/wepay_laravel.git"
    }
],
"require": {
    "wepaycheckout/laravel": "^1.0"
}
```

For the **default branch** before you tag releases:

```json
"minimum-stability": "dev",
"prefer-stable": true,
"require": {
    "wepaycheckout/laravel": "dev-main"
}
```

Then:

```bash
composer update wepaycheckout/laravel
php artisan vendor:publish --tag=wepay-config
```

Copy variables from `.env.example` in this package into your app `.env`.

## Release versioning

Tag stable versions so consumers can use `^1.0`:

```bash
git tag -a v1.0.0 -m "1.0.0"
git push origin v1.0.0
```
