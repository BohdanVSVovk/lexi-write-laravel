<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\{
    UserController,
    DashboardController,
    SubscriptionController
};

/*
|--------------------------------------------------------------------------
| User Panel Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::prefix('user')->name('user.')->middleware(['auth', 'locale'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [UserController::class, 'update'])->middleware(['checkForDemoMode'])->name('userProfileUpdate');
    Route::post('/profile/update-password', [UserController::class, 'updatePassword'])->middleware(['checkForDemoMode'])->name('userProfileUpdatePassword');

    Route::post('/profile/verify-email', [UserController::class, 'verifyEmailByAjax'])->middleware(['checkForDemoMode'])->name('userProfileEmailVerifyByAjax');
    Route::post('/profile/verification-otp', [UserController::class, 'verifyOtpByAjax'])->middleware(['checkForDemoMode'])->name('userProfileOtpVerifyByAjax');
    Route::post('/profile/update-email', [UserController::class, 'updateEmailByAjax'])->middleware(['checkForDemoMode'])->name('userProfileUpdateEmailByAjax');

    Route::get('/profile/verification/{token}', [UserController::class, 'verification'])->name('userProfileVerification');

    Route::get('/subscription', [UserController::class, 'subscription'])->name('subscription');
    Route::get('/subscription/history', [UserController::class, 'subscriptionHistory'])->name('subscription.history');

    Route::get('subscription/package', [SubscriptionController::class, 'package'])->name('package');
    Route::post('subscription/store', [SubscriptionController::class, 'storeSubscription'])->name('subscription.store');
    Route::post('subscription/update', [SubscriptionController::class, 'updateSubscription'])->name('subscription.update');
    Route::get('subscription/cancel/{user_id}', [SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel');
    Route::get('bill-history/show/{id}', [SubscriptionController::class, 'billDetails'])->name('bill-details');
    Route::get('bill-history/pdf/{id}', [SubscriptionController::class, 'billPdf'])->name('bill-pdf');

    Route::post('subscription/pending-payment', [SubscriptionController::class, 'payPendingSubscription'])->name('pay.pending_subscription');
});

Route::get('/profile/edit-email/{id}', [UserController::class, 'editEmail'])->name('userEditEmail');
Route::post('/profile/update-new-email', [UserController::class, 'updateEmail'])->name('userUpdateEmail');
Route::get('plan-description/{id}', [SubscriptionController::class, 'planDescription'])->name('plan.description');
Route::get('subscription-paid', [SubscriptionController::class, 'subscriptionPaid'])->name('subscription-paid');
Route::get('subscription-update-paid', [SubscriptionController::class, 'subscriptionUpdatePaid'])->name('subscription-update-paid');

Route::get('subscription-pending-payment-response', [SubscriptionController::class, 'subscriptionPendingPaymentResponse'])->name('subscription-pending-payment-response');











