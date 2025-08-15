<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

Route::get('/booking-form', function () {
    return view('booking-form');
});

Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
