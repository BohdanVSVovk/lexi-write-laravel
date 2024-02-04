<?php

use Illuminate\Support\Facades\Route;
use Modules\OpenAI\Entities\Chat;
use Modules\OpenAI\Http\Controllers\Admin\{
    UseCasesController,
    UseCaseCategoriesController,
    OpenAIController,
    ImageController,
    CodeController
};
use Modules\OpenAI\Http\Controllers\Customer\{
    OpenAIController as UserAIController,
    ImageController as UserImageController,
    UseCasesController as CustomerUseCasesController,
    DocumentsController as CustomerDocumentsController,
    CodeController as CustomerCodeController,
    ChatController
};

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


Route::get('/user/templates/', [UserAIController::class, 'templates'])->name('openai')->middleware(['auth', 'locale']);

Route::prefix('user')->middleware(['auth', 'locale'])->name('user.')->group(function () {

    Route::get('formfiled-usecase/{slug}', [UserAIController::class, 'getFormFiledByUsecase'])->name('formField');
    Route::get('get-content', [UserAIController::class, 'getContent']);
    Route::get('deleteContent', [UserAIController::class, 'deleteContent'])->name('deleteContent');
    Route::get('content/edit/{slug}', [UserAIController::class, 'editContent'])->name('editContent');
    Route::post('update-content', [UserAIController::class, 'updateContent'])->name('updateContent');

    Route::get('documents', [UserAIController::class, 'documents'])->name('documents');
    Route::get('favourite-documents', [UserAIController::class, 'favouriteDocuments'])->name('favouriteDocuments');
    Route::get('templates/{slug}', [UserAIController::class, 'template'])->name('template');
    Route::get('image', [UserAIController::class, 'imageTemplate'])->name('imageTemplate');
    Route::get('code', [UserAIController::class, 'codeTemplate'])->name('codeTemplate');
    Route::post('delete-image', [UserImageController::class, 'deleteImage'])->name('deleteImage');
    Route::post('save-image', [UserImageController::class, 'saveImage'])->name('saveImage');
    Route::get('image-list', [UserImageController::class, 'list'])->name('imageList');
    Route::get('image/view/{slug}', [UserImageController::class, 'view'])->name('image.view');
    Route::get('code-list', [CustomerCodeController::class, 'index'])->name('codeList');
    Route::get('code/view/{slug}', [CustomerCodeController::class, 'view'])->name('codeView');
    Route::post('code/delete/', [CustomerCodeController::class, 'delete'])->name('deleteCode');

    // Chat

    Route::get('chat-history/{id}', [ChatController::class, 'history'])->name('chat');
    Route::post('delete-chat', [ChatController::class, 'delete'])->name('deleteChat');
    Route::post('update-chat', [ChatController::class, 'update'])->name('updateChat');
});


Route::middleware(['auth', 'locale'])->prefix('admin')->group(function () {
    Route::name('admin.use_case.')->group(function() {
        // use case
        Route::get('/use-cases', [UseCasesController::class, 'index'])->name('list');
        Route::match(['get', 'post'], '/use-case/create', [UseCasesController::class, 'create'])->name('create');
        Route::match(['get', 'post'], '/use-case/{id}/edit', [UseCasesController::class, 'edit'])->name('edit');
        Route::post('/use-case/{id}/delete', [UseCasesController::class, 'destroy'])->middleware(['checkForDemoMode'])->name('destroy');

        // use case category
        Route::get('/use-case/categories', [UseCaseCategoriesController::class, 'index'])->name('category.list');
        Route::match(['get', 'post'], '/use-case/category/create', [UseCaseCategoriesController::class, 'create'])->name('category.create');
        Route::match(['get', 'post'], '/use-case/category/{id}/edit', [UseCaseCategoriesController::class, 'edit'])->name('category.edit');
        Route::post('/use-case/category/{id}/delete', [UseCaseCategoriesController::class, 'destroy'])->middleware(['checkForDemoMode'])->name('category.destroy');
        Route::get('/use-case/category/search', [UseCaseCategoriesController::class, 'searchCategory'])->name('category.search');
    });

    Route::name('admin.features.')->group(function() {
        // Content
        Route::get('content/list', [OpenAIController::class, 'index'])->name('contents');
        Route::get('content/edit/{slug}', [OpenAIController::class, 'edit'])->name('content.edit');
        Route::post('content/update/{id}', [OpenAIController::class, 'update'])->middleware(['checkForDemoMode'])->name('content.update');
        Route::get('content/delete', [OpenAIController::class, 'delete'])->middleware(['checkForDemoMode'])->name('content.delete');

        // Image
        Route::post('delete-images', [ImageController::class, 'deleteImages'])->middleware(['checkForDemoMode'])->name('deleteImage');
        Route::post('save-image', [ImageController::class, 'saveImage'])->name('saveImage');
        Route::get('image/list', [ImageController::class, 'list'])->name('imageList');
        Route::get('image/view/{slug}', [ImageController::class, 'view'])->name('image.view');

        // Code
        Route::get('code/list', [CodeController::class, 'index'])->name('code.list');
        Route::get('code/view/{slug}', [CodeController::class, 'view'])->name('code.view');
        Route::post('code/delete', [CodeController::class, 'delete'])->middleware(['checkForDemoMode'])->name('code.delete');

        // Content Preferences
        Route::get('features/preferences', [OpenAIController::class, 'contentPreferences'])->name('preferences');
        Route::post('features/preferences/create', [OpenAIController::class, 'createContentPreferences'])->middleware(['checkForDemoMode'])->name('preferences.create');
    });
});

Route::middleware(['auth', 'locale'])->prefix('user/openai')->name('user.')->group(function () {
    Route::get('/use-case/search', [CustomerUseCasesController::class, 'searchTabData'])->name('use_case.search');
    Route::post('/use-case/toggle/favorite', [CustomerUseCasesController::class, 'toggleFavorite'])->name('use_case.toggle.favorite');
    Route::get('/documents/fetch', [CustomerDocumentsController::class, 'fetchAndFilter'])->name('document.fetch');
    Route::post('/documents/toggle/bookmark', [CustomerDocumentsController::class, 'toggleBookmark'])->name('document.toggle.bookmark');
});
