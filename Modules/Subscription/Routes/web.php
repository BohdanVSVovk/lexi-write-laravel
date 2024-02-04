<?php

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

Route::group(['prefix' => 'admin', 'namespace' => 'Modules\Subscription\Http\Controllers', 'middleware' => ['auth', 'locale', 'permission']], function () {
    // Package
    Route::get('packages', 'PackageController@index')->name('package.index');
    Route::get('packages/create', 'PackageController@create')->name('package.create');
    Route::post('packages', 'PackageController@store')->middleware(['checkForDemoMode'])->name('package.store');
    Route::get('packages/{id}/edit', 'PackageController@edit')->name('package.edit');
    Route::put('packages/{id}', 'PackageController@update')->middleware(['checkForDemoMode'])->name('package.update');
    Route::delete('packages/{id}', 'PackageController@destroy')->middleware(['checkForDemoMode'])->name('package.destroy');

    Route::get('package/get-templates/{categoryId}', 'PackageController@getTemplate');
    Route::get('package/get-info/{id}', 'PackageController@getInfo');
    Route::get('package/get-chats/{categoryId}', 'PackageController@getChat');

    // Credit
    Route::get('credits', 'CreditController@index')->name('credit.index');
    Route::get('credits/create', 'CreditController@create')->name('credit.create');
    Route::post('credits', 'CreditController@store')->middleware(['checkForDemoMode'])->name('credit.store');
    Route::get('credits/{id}/edit', 'CreditController@edit')->name('credit.edit');
    Route::put('credits/{id}', 'CreditController@update')->middleware(['checkForDemoMode'])->name('credit.update');
    Route::delete('credits/{id}', 'CreditController@destroy')->middleware(['checkForDemoMode'])->name('credit.destroy');

    // Package Subscription
    Route::get('package-subscriptions', 'PackageSubscriptionController@index')->name('package.subscription.index');
    Route::get('package-subscriptions/create', 'PackageSubscriptionController@create')->name('package.subscription.create');
    Route::post('package-subscriptions', 'PackageSubscriptionController@store')->middleware(['checkForDemoMode'])->name('package.subscription.store');
    Route::get('package-subscriptions/{id}', 'PackageSubscriptionController@show')->name('package.subscription.show');
    Route::get('package-subscriptions/{id}/edit', 'PackageSubscriptionController@edit')->name('package.subscription.edit');
    Route::put('package-subscriptions/{id}', 'PackageSubscriptionController@update')->middleware(['checkForDemoMode'])->name('package.subscription.update');
    Route::delete('package-subscriptions/{id}', 'PackageSubscriptionController@destroy')->middleware(['checkForDemoMode'])->name('package.subscription.destroy');
    Route::match(['get', 'post'], 'subscriptions/settings', 'PackageSubscriptionController@setting')->name('package.subscription.setting');

    Route::get('payments', 'PackageSubscriptionController@payment')->name('package.subscription.payment');
    Route::put('payments/{id}/paid', 'CreditController@paid')->name('payment.paid');

    Route::get('package-subscription/{id}/invoice', 'PackageSubscriptionController@invoice')->name('package.subscription.invoice');
    Route::get('package-subscription/{id}/invoice/pdf', 'PackageSubscriptionController@invoicePdf')->name('package.subscription.invoice.pdf');
    Route::get('package-subscription/{id}/invoice/email', 'PackageSubscriptionController@invoiceEmail')->name('package.subscription.invoice.email');
});
