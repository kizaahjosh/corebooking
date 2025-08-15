<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/booking-form', function () {
    return view('booking-form');
});

Route::get('/admin/bookings', function () {
    return view('admin-bookings');
});

Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
