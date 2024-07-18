<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\WebBlogController;
use App\Http\Controllers\Web\WebBookingController;
use App\Http\Controllers\Web\WebContactController;

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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/blogs', [WebBlogController::class, 'index'])->name('blogs');
Route::get('/contacts', [WebContactController::class, 'index'])->name('contacts');
Route::post('/contact-mail', [ContactController::class, 'sendMail'])->name('send-mail');
Route::get('/blog-detail/{blog}', [WebBlogController::class, 'blogDetailPage'])->name('blog-detail');
Route::get('/bookings', [WebBookingController::class, 'index'])->name('bookings');
Route::post('/confirm-booking', [WebBookingController::class, 'sendBooking'])->name('send-booking');


Auth::routes();

Route::get('/forgot-password', 'App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('/forgot-password', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

Route::post('/do-login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('admin.do-login');

Route::group(['middleware' => ['auth:admin']], function () {
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('admin.logout');
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('admin.dashboard');

    Route::group(['prefix' => 'blog', 'namespace' => 'Blog', 'as' => 'blog.'], function () {
        Route::get('/', [BlogController::class, 'index'])->name('index');
        Route::get('/create', [BlogController::class, 'create'])->name('create');
        Route::post('/store', [BlogController::class, 'store'])->name('store');
        Route::get('/edit/{blog}', [BlogController::class, 'edit'])->name('edit');
        Route::post('/update/{blog}', [BlogController::class, 'update'])->name('update');
        Route::delete('/delete/{blog}', [BlogController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'booking', 'namespace' => 'Booking', 'as' => 'booking.'], function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/create', [BookingController::class, 'create'])->name('create');
        Route::post('/store', [BookingController::class, 'store'])->name('store');
        Route::get('/edit/{booking}', [BookingController::class, 'edit'])->name('edit');
        Route::post('/update/{booking}', [BookingController::class, 'update'])->name('update');
        Route::delete('/delete/{booking}', [BookingController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'slider', 'namespace' => 'Slider', 'as' => 'slider.'], function () {
        Route::get('/', [SliderController::class, 'index'])->name('index');
        Route::get('/create', [SliderController::class, 'create'])->name('create');
        Route::post('/store', [SliderController::class, 'store'])->name('store');
        Route::get('/edit/{banner}', [SliderController::class, 'edit'])->name('edit');
        Route::post('/update/{banner}', [SliderController::class, 'update'])->name('update');
        Route::delete('/delete/{banner}', [SliderController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'contact', 'namespace' => 'Contact', 'as' => 'contact.'], function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::delete('/delete/{contact}', [ContactController::class, 'delete'])->name('delete');
    });
});
