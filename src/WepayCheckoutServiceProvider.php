<?php

namespace WepayCheckout;

use Illuminate\Support\ServiceProvider;

/**
 * @author Dridi Hatem <dridihatem@gmail.com>
 * @link https://dawebcompany.tn
 */
class WepayCheckoutServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/wepay.php', 'wepay');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/wepay.php' => config_path('wepay.php'),
        ], 'wepay-config');

        $this->loadRoutesFrom(__DIR__ . '/../routes/wepay.php');
    }
}
