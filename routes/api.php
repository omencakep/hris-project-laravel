<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\FeedbackController;


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
});

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);

    Route::get('/attendance/recent', [AttendanceController::class, 'recentAttendances'])->name('attendance.recent');
    Route::get('/attendance/today', [AttendanceController::class, 'todayAttendance'])->name('attendance.today');

    Route::post('/feedback', [FeedbackController::class, 'submitFeedback']);
    Route::get('/feedback/sentiment-count', [FeedbackController::class, 'countSentiment']);

    Route::get('/office-location', function () {
        return response()->json([
            'latitude' => (float) env('OFFICE_LATITUDE'),
            'longitude' => (float) env('OFFICE_LONGITUDE'),
            'radius' => (float) env('OFFICE_RADIUS'),
        ]);
    });
});
