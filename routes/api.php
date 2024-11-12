<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\PrintReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['namespace' => 'Api', 'name' => 'api'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('qr-login', [AuthController::class, 'qrLogin']);
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('dashboard', [RouteController::class, 'getDashboard']);
        Route::post('get-route', [RouteController::class, 'getDeviceRoute']);
        Route::post('get-route', [RouteController::class, 'getDeviceRoute']);
        Route::post('get-stages', [RouteController::class, 'getStages']);
        Route::post('book-ticket', [RouteController::class, 'bookTicket']);
        Route::post('get-previous-booking', [RouteController::class, 'getPreviousBooking']);
        Route::post('get-trip-report', [RouteController::class, 'getTripReport']);
        Route::post('stage-report', [RouteController::class, 'stageWiseReport']);
        Route::post('inspector-report', [RouteController::class, 'inspectorReport']);
        Route::post('fare-report', [RouteController::class, 'fareReport']);
        Route::post('start-trip', [RouteController::class, 'startTrip']);
        Route::post('end-trip', [RouteController::class, 'endTrip']);
        Route::post('manage-offline-trip', [RouteController::class, 'manageOfflineTrip']);
        Route::post('start-day', [RouteController::class, 'startDay']);
        Route::post('end-day', [RouteController::class, 'endDay']);
        Route::post('get-trip-details', [RouteController::class, 'getTripDetails']);
        Route::post('previous-collection', [RouteController::class, 'previousCollection']);
        Route::post('get-collection-report', [RouteController::class, 'getCollectionReport']);
        Route::post('submit-cleaner-amount', [RouteController::class, 'submitCleanerAmount']);
        Route::post('manage-trip', [RouteController::class, 'manageTrip']);
        Route::post('generate-qr',[RouteController::class, 'generateQrCode']);
        Route::post('logout', [AuthController::class, 'logOut']);

        // Print Reports
        Route::post('print-trip-report', [PrintReportController::class, 'printTripReport']);
    });

    Route::post('owner-login', [AuthController::class, 'ownerLogin']);

    Route::group(['middleware' => ['auth:manager', 'checkTokenExpiration']], function () {
        Route::post('owner-logout', [AuthController::class, 'ownerLogout']);
    });
});
