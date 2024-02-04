<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\Http\Controllers\Api\V1\PackageController;
use Modules\Subscription\Http\Controllers\Api\V1\User\PackageSubscriptionController;

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

Route::group(['prefix' => '/V1/user/subscriptions', 'middleware' => ['auth:api', 'locale', 'permission-api', 'permission']], function() {
    // Subscription
    Route::get('/', [PackageSubscriptionController::class, 'detail']);
    Route::get('/history', [PackageSubscriptionController::class, 'history']);
    Route::get('/history/{id}/bill/view', [PackageSubscriptionController::class, 'viewBill']);
    Route::post('/history/{id}/bill/pay', [PackageSubscriptionController::class, 'payBill']);
    Route::get('/history/{id}/bill/download', [PackageSubscriptionController::class, 'downloadBill']);
    Route::post('/cancel', [PackageSubscriptionController::class, 'cancel']);
    Route::get('/plan', [PackageSubscriptionController::class, 'plan']);
    Route::post('/store', [PackageSubscriptionController::class, 'store']);
    Route::get('/setting', [PackageSubscriptionController::class, 'setting']);
});

Route::group(['prefix' => '/V1/plans', 'middleware' => ['locale']], function() {
    // Plan
    Route::get('/', [PackageController::class, 'index']);
});
