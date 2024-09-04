<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\AdminRouteController;
use App\Http\Controllers\Admin\AdminDeviceController;
use App\Http\Controllers\Admin\AdminManagerController;

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

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

Auth::routes();

Route::get('/forgot-password', 'App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('/forgot-password', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

Route::post('/do-login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('admin.do-login');

Route::group(['middleware' => ['auth:admin']], function () {
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('admin.logout');
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    // Manager
    Route::group(['prefix' => 'manager', 'namespace' => 'Manager', 'as' => 'manager.'], function () {
        Route::get('/', [AdminManagerController::class, 'index'])->name('index');
        Route::post('/store', [AdminManagerController::class, 'store'])->name('store');
        Route::get('/edit/{manager}', [AdminManagerController::class, 'edit'])->name('edit');
        Route::put('/update/{manager}', [AdminManagerController::class, 'update'])->name('update');
        Route::delete('/delete/{manager}', [AdminManagerController::class, 'delete'])->name('destroy');
    });

    // Manager device
    Route::group(['prefix' => 'manager-device', 'namespace' => 'Manager-Device', 'as' => 'manager-device.'], function () {
        Route::get('/{manager}', [AdminDeviceController::class, 'index'])->name('index');
        Route::post('/store', [AdminDeviceController::class, 'store'])->name('store');
        Route::get('/edit/{device}', [AdminDeviceController::class, 'edit'])->name('edit');
        Route::put('/update/{device}', [AdminDeviceController::class, 'update'])->name('update');
        Route::delete('/delete/{device}', [AdminDeviceController::class, 'delete'])->name('destroy');
    });

    // Device
    Route::group(['prefix' => 'device', 'namespace' => 'Device', 'as' => 'device.'], function () {
        Route::get('/', [DeviceController::class, 'index'])->name('index');
        Route::delete('/delete/{device}', [DeviceController::class, 'delete'])->name('destroy');
    });

    // Route
    Route::group(['prefix' => 'route', 'namespace' => 'Route', 'as' => 'route.'], function () {
        Route::get('/', [AdminRouteController::class, 'index'])->name('index');
        Route::post('/store', [AdminRouteController::class, 'store'])->name('store');
        Route::get('/edit/{route}', [AdminRouteController::class, 'edit'])->name('edit');
        Route::put('/update/{route}', [AdminRouteController::class, 'update'])->name('update');
        Route::delete('/delete/{route}', [AdminRouteController::class, 'delete'])->name('destroy');
    });

});

