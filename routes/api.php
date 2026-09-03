<?php

use App\Http\Controllers\API\Master\OTPGeneratorController;
use App\Http\Controllers\API\Master\OTPValidatorController;
use App\Http\Controllers\API\Settings\AppConfigController;
use App\Http\Controllers\API\Settings\OTPConfigController;
use App\Http\Controllers\API\Settings\OTPLengthController;
use App\Http\Controllers\API\Settings\OTPTypeController;
use App\Http\Controllers\API\Settings\ResponseController;
use App\Http\Controllers\API\Payment\PaymentController;
use App\Http\Controllers\API\AppController as ApiAppController;
use App\Http\Controllers\API\PaymentGatewayController as ApiPaymentGatewayController;
use App\Http\Controllers\API\LogController as ApiLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
   
Route::get('/refresh', [AppConfigController::class, 'executeCommands']);


Route::group(['middleware' => 'api.auth'], function () {
    // OTP Master
    Route::controller(OTPGeneratorController::class)->group(function () {
        Route::prefix('master')->group(function () {
            // Route::get('/', 'index');
            Route::post('/generator', 'generateOTP');
        });
    });

    // OTP Validator
    Route::controller(OTPValidatorController::class)->group(function () {
        Route::prefix('master')->group(function () {
            Route::post('/validator', 'validateOTP');
        });
    });
});

// Master
Route::controller(OTPGeneratorController::class)->group(function () {
    Route::prefix('master')->group(function () {
        Route::get('/', 'index');
    });
});

// App Management
Route::prefix('apps')->group(function () {
    Route::get('/', [ApiAppController::class, 'index']);
    Route::get('/gateways', [ApiAppController::class, 'gateways']);
    Route::get('/{id}', [ApiAppController::class, 'show']);
    Route::post('/', [ApiAppController::class, 'store']);
    Route::put('/{id}', [ApiAppController::class, 'update']);
    Route::delete('/{id}', [ApiAppController::class, 'destroy']);
    Route::post('/{id}/test', [ApiAppController::class, 'runTest']);
});


// App Configuration
Route::controller(AppConfigController::class)->group(function () {
    Route::prefix('settings')->group(function () {
        Route::get('/refresh', 'executeCommands');
        Route::get('/app_config', 'index');
        Route::get('/app_config/active', 'viewActive');
        Route::get('/app_config/not-active', 'viewNotActive');
        Route::get('/app_config/gateways', 'gateways');
        Route::get('/app_config/{id}', 'show');
        Route::post('/app_config/create', 'store');
        Route::put('/app_config/update/{id}', 'update');
        Route::delete('/app_config/delete/{id}', 'delete');
        Route::post('/app_config/{id}/run-test', 'runTest');
    });
});

// OTP Configuration
Route::controller(OTPConfigController::class)->group(function () {
    Route::prefix('settings')->group(function () {
        Route::get('/otp_config', 'index');
        // Route::post('/otp-config/create', 'store');
        Route::put('/otp_config/update/{id}', 'update');
        // Route::delete('/otp-config/delete/{id}', 'destroy');
    });
});

// Responses
Route::controller(ResponseController::class)->group(function () {
    Route::prefix('settings')->group(function () {
        Route::get('/responses', 'index');
        Route::post('/response/create', 'store');
        Route::put('/response/update/{id}', 'update');
        Route::delete('/response/delete/{id}', 'destroy');
    });
});

// OTP Length
Route::controller(OTPLengthController::class)->group(function () {
    Route::prefix('settings')->group(function () {
        Route::get('/otp_length', 'index');
        // Route::post('/otp_length/create', 'store');
        Route::put('/otp_length/update/{id}', 'update');
        Route::delete('/otp_length/delete/{id}', 'destroy');
    });
});

// OTP Type
Route::controller(OTPTypeController::class)->group(function () {
    Route::prefix('settings')->group(function () {
        Route::get('/otp_type', 'index');
        // Route::post('/otp_type/create', 'store');
        Route::put('/otp_type/update/{id}', 'update');
        Route::delete('/otp_type/delete/{id}', 'destroy');
    });
});

// Payment Integration
Route::controller(PaymentController::class)->group(function () {
    Route::prefix('payment')->group(function () {
        Route::post('/initiate', 'initiate');
        Route::get('/verify/{reference}', 'verify');
        Route::get('/callback/{gateway}', 'callback')->name('api.payment.callback');
        Route::post('/webhook/{gateway}', 'webhook');
    });
});

// Payment Gateways Configuration Endpoints
Route::prefix('payment-gateways')->group(function () {
    Route::get('/', [ApiPaymentGatewayController::class, 'index']);
    Route::get('/transactions', [ApiPaymentGatewayController::class, 'transactionsOnly']);
    Route::get('/{gatewayId}', [ApiPaymentGatewayController::class, 'show']);
    Route::put('/{gatewayId}', [ApiPaymentGatewayController::class, 'update']);
    Route::get('/app-config/{appId}', [ApiPaymentGatewayController::class, 'appConfig']);
    Route::post('/app-config/{appId}', [ApiPaymentGatewayController::class, 'saveAppConfig']);
});

// Logs and Response Configuration Endpoints
Route::get('/logs/history', [ApiLogController::class, 'getHistory']);
Route::get('/logs/unified', [ApiLogController::class, 'getUnifiedLogs']);
Route::get('/logs/unified/{appID}', [ApiLogController::class, 'getUnifiedLogsByAppID']);
Route::get('/responses', [ApiLogController::class, 'getResponses']);
Route::get('/logs/api-requests', [ApiLogController::class, 'getApiRequestLogs']);

// Checkout JSON API (consumed by React frontend)
Route::prefix('checkout')->group(function () {
    Route::get('/{reference}', [\App\Http\Controllers\API\CheckoutApiController::class, 'show']);
    Route::post('/{reference}/select', [\App\Http\Controllers\API\CheckoutApiController::class, 'select']);
});

// Authentication Endpoints
Route::prefix('auth')->group(function () {
    Route::post('/login', [\App\Http\Controllers\API\AuthController::class, 'login']);
    Route::post('/forgot-password', [\App\Http\Controllers\API\AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [\App\Http\Controllers\API\AuthController::class, 'resetPassword']);
    Route::post('/logout', [\App\Http\Controllers\API\AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// Named Password Reset route for Laravel default notification generator
Route::get('/reset-password/{token}', function (\Illuminate\Http\Request $request, $token) {
    $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
    return redirect($frontendUrl . '/reset-password?token=' . $token . '&email=' . $request->query('email'));
})->name('password.reset');

