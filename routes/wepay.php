<?php

use Illuminate\Support\Facades\Route;
use WepayCheckout\Http\Controllers\WepayInvoiceController;
use WepayCheckout\Http\Controllers\WepayWebhookController;

$prefix = (string) config('wepay.route_prefix', 'wepay');

/*
| Webhook + JSON status: `api` middleware (no CSRF). Invoice create: `web` (CSRF for session forms).
*/
Route::middleware('api')->prefix($prefix)->group(function () {
    Route::post('/webhook', WepayWebhookController::class)->name('wepay.webhook');
    Route::get('/invoice/{invoiceId}/status', [WepayInvoiceController::class, 'status'])
        ->name('wepay.invoice.status');
});

Route::middleware('web')->prefix($prefix)->group(function () {
    Route::post('/invoice/create', [WepayInvoiceController::class, 'create'])
        ->name('wepay.invoice.create');
});
