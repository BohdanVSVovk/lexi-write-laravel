<?php

use Illuminate\Support\Facades\Route;
use Modules\OpenAI\Http\Controllers\Api\V1\Admin\{
    UseCasesController as adminUsecaseApi,
    UseCaseCategoriesController as adminUseCaseCategoryApi,
    OpenAIController as adminApi,
    ImageController as adminImageApi,
    CodeController as adminCodeApi,
};
use Modules\OpenAI\Http\Controllers\Api\V1\User\{
    OpenAIController,
    ImageController,
    UseCasesController,
    UseCaseCategoriesController,
    CodeController,
    OpenAIPreferenceController,
    ChatController,
    UserController,
};

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


Route::group(['prefix' => '/V1/user/openai', 'middleware' => ['auth:api', 'locale']], function() {
    Route::post('chat', [OpenAIController::class, 'chat']);
    Route::get('chat/conversation', [OpenAIController::class, 'chatConversation']);
    Route::get('chat/history/{id}', [OpenAIController::class, 'history']);

    Route::post('chat/delete', [ChatController::class, 'delete']);
    Route::post('chat/update', [ChatController::class, 'update']);
});

Route::group(['prefix' => '/V1/user/openai', 'middleware' => ['auth:api', 'locale', 'permission-api', 'permission']], function() {
    // Content
    Route::get('content/list', [OpenAIController::class, 'index']);
    Route::get('content/view/{slug}', [OpenAIController::class, 'view']);
    Route::post('content/edit/{slug}', [OpenAIController::class, 'update']);
    Route::delete('content/delete/{id}', [OpenAIController::class, 'delete']);

    // Image
    Route::get('image/list', [ImageController::class, 'index']);
    Route::delete('image/delete', [ImageController::class, 'delete']);
    Route::get('image/view/{id}', [ImageController::class, 'view']);

    // Create content and image
    Route::post('ask', [OpenAIController::class, 'ask']);
    Route::post('image', [OpenAIController::class, 'image']);
    Route::post('code', [OpenAIController::class, 'code']);
   

    // use case
    Route::get('/use-cases', [UseCasesController::class, 'index']);
    Route::post('/use-case/create', [UseCasesController::class, 'create']);
    Route::get('/use-case/{id}/show', [UseCasesController::class, 'show']);
    Route::put('/use-case/{id}/edit', [UseCasesController::class, 'edit']);
    Route::delete('/use-case/{id}/delete', [UseCasesController::class, 'destroy']);

     // use case category
    Route::get('/use-case/categories', [UseCaseCategoriesController::class, 'index']);
    Route::post('/use-case/category/create', [UseCaseCategoriesController::class, 'create']);
    Route::get('/use-case/category/{id}/show', [UseCaseCategoriesController::class, 'show']);
    Route::put('/use-case/category/{id}/edit', [UseCaseCategoriesController::class, 'edit']);
    Route::delete('/use-case/category/{id}/delete', [UseCaseCategoriesController::class, 'destroy']);

    // Code
    Route::get('code/list', [CodeController::class, 'index']);
    Route::get('code/view/{slug}', [CodeController::class, 'view']);
    Route::delete('code/delete/{id}', [CodeController::class, 'delete']);

    //Content Preferences
    Route::get('preferences/content', [OpenAIPreferenceController::class, 'contentPreferences']);
    Route::get('preferences/image', [OpenAIPreferenceController::class, 'imagePreferences']);
    Route::get('preferences/code', [OpenAIPreferenceController::class, 'codePreferences']);

    //Update Profile
    Route::post('/profile', [UserController::class, 'update']);

    //Subscription Package Info
    Route::get('/package-info', [UserController::class, 'index']);

});

Route::group(['prefix' => '/V1/admin/openai', 'middleware' => ['auth:api', 'locale', 'permission']], function() {
    // Content
    Route::get('content/list', [adminApi::class, 'index']);
    Route::get('content/view/{slug}', [adminApi::class, 'view']);
    Route::post('content/edit/{slug}', [adminApi::class, 'update']);
    Route::delete('content/delete/{id}', [adminApi::class, 'delete']);

    // Image
    Route::get('image/list', [adminImageApi::class, 'index']);
    Route::delete('image/delete', [adminImageApi::class, 'delete']);
    Route::get('image/view/{id}', [adminImageApi::class, 'view']);

    // Create content and image
    Route::post('ask', [adminApi::class, 'ask']);
    Route::post('image', [adminApi::class, 'image']);
    Route::post('code', [adminApi::class, 'code']);

    // use case
    Route::get('/use-cases', [adminUsecaseApi::class, 'index']);
    Route::post('/use-case/create', [adminUsecaseApi::class, 'create']);
    Route::get('/use-case/{id}/show', [adminUsecaseApi::class, 'show']);
    Route::put('/use-case/{id}/edit', [adminUsecaseApi::class, 'edit']);
    Route::delete('/use-case/{id}/delete', [adminUsecaseApi::class, 'destroy']);

    // use case category
    Route::get('/use-case/categories', [adminUseCaseCategoryApi::class, 'index']);
    Route::post('/use-case/category/create', [adminUseCaseCategoryApi::class, 'create']);
    Route::get('/use-case/category/{id}/show', [adminUseCaseCategoryApi::class, 'show']);
    Route::put('/use-case/category/{id}/edit', [adminUseCaseCategoryApi::class, 'edit']);
    Route::delete('/use-case/category/{id}/delete', [adminUseCaseCategoryApi::class, 'destroy']);

    // Code
    Route::get('code/list', [adminCodeApi::class, 'index']);
    Route::get('code/view/{slug}', [adminCodeApi::class, 'view']);
    Route::delete('code/delete/{id}', [adminCodeApi::class, 'delete']);

});
