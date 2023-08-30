<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\HomepageController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ManufacturerController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TesseractOCRController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Route::prefix('/admin')->group(function () {
        Route::get('/', [HomepageController::class, 'index']);
        Route::resource('/homepage', HomepageController::class);
        Route::post('/products/update-status-product/{productId}', [ProductController::class, 'updateStatusProduct'])->name('update-status-product');
        Route::get('/products/filter-product', [ProductController::class, 'filterProduct'])->name('filter-product');
        Route::post('/products/copy-product', [ProductController::class, 'copyProduct'])->name('copy-product');
        Route::delete('/products/delete-product', [ProductController::class, 'deleteProduct'])->name('delete-product');
        Route::resource('/products', ProductController::class);
        Route::post('/categories/update-status-category/{categoryId}', [CategoryController::class, 'updateStatusCategory'])->name('update-status-category');
        Route::delete('/categories/delete-category', [CategoryController::class, 'deleteCategory'])->name('delete-category');
        Route::resource('/categories', CategoryController::class);
        Route::resource('/manufacturers', ManufacturerController::class);
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/setting/general_setting', [SettingController::class, 'index'])->name('general-setting');
        Route::resource('/account', AccountController::class);
        Route::get('/option', [TesseractOCRController::class, 'index'])->name('option.index');
        Route::post('/option/scan-img', [TesseractOCRController::class, 'processImage'])->name('option.processImage');
    });
});

Route::prefix('/admin')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'loginAuthenticate'])->name('login');

    Route::get('/reset', [AccountController::class, 'showEmailForm'])->name('show-email-form');
    Route::post('/reset_password_without_token', [AccountController::class, 'validatePasswordRequest'])->name('without-token');
    Route::get('/reset_password', [AccountController::class, 'showResetPasswordForm'])->name('show-reset-password-form');
    Route::match(['post', 'put'], '/reset_password_with_token/{token?}', [AccountController::class, 'resetPassword'])->name('with-token');
});



Route::get('/{any}', function () {
    return view('error.error');
})->where('any', '.*');

