<?php
use App\Http\Controllers\BookingController;

Route::post('/bookings', [BookingController::class, 'store']);
