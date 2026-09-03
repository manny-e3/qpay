<?php

use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\AppController;
use App\Http\Controllers\Web\Admin\SettingsController;
use App\Http\Controllers\Web\Admin\LogController;
use App\Http\Controllers\Web\Admin\AuthController;
use App\Http\Controllers\Web\Admin\PaymentGatewayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return "Cache cleared successfully!";
});


Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Admin Routes
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('apps', AppController::class);
    Route::get('/apps/{app}/test', [AppController::class, 'test'])->name('apps.test');
    Route::post('/apps/{app}/test', [AppController::class, 'runTest'])->name('apps.run-test');
    
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/duration', [SettingsController::class, 'updateOTPConfig'])->name('settings.update-duration');
    Route::put('/settings/length', [SettingsController::class, 'updateOTPLength'])->name('settings.update-length');
    
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

    // Payment Management
    Route::get('/payment', [PaymentGatewayController::class, 'index'])->name('payment.index');
    Route::get('/payment/transactions', [PaymentGatewayController::class, 'transactions'])->name('payment.transactions');
    Route::get('/payment/gateways/{gateway}/edit', [PaymentGatewayController::class, 'edit'])->name('payment.gateways.edit');
    Route::put('/payment/gateways/{gateway}', [PaymentGatewayController::class, 'update'])->name('payment.gateways.update');
    Route::get('/apps/{app}/payment', [PaymentGatewayController::class, 'configureApp'])->name('apps.payment');
    Route::post('/apps/{app}/payment', [PaymentGatewayController::class, 'saveAppConfig'])->name('apps.payment.save');
});

// Test Payment Page
Route::get('/test-payment', [\App\Http\Controllers\Web\TestPaymentController::class, 'index'])->name('test.payment');

// Checkout Selection Routes
Route::get('/checkout/{reference}', [\App\Http\Controllers\Web\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/{reference}/select', [\App\Http\Controllers\Web\CheckoutController::class, 'select'])->name('checkout.select');
