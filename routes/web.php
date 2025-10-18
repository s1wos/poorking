<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ServiceController, SlotController, BookingController};
use Inertia\Inertia;

Route::get('/', function () {
    return app(ServiceController::class)->index();
});

Route::get('/services/{service}', [ServiceController::class, 'show']);
Route::get('/services/{service}/slots', [SlotController::class, 'index']);
Route::post('/bookings', [BookingController::class, 'store']);
