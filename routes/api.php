<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

// Booking API Routes
Route::prefix('bookings')->group(function () {
    Route::post('/', [BookingController::class, 'store']);           // Create booking
    Route::get('/', [BookingController::class, 'index']);            // Get all bookings
    Route::get('/{id}', [BookingController::class, 'show']);         // Get specific booking
    Route::patch('/{id}/status', [BookingController::class, 'updateStatus']); // Update booking status
    Route::delete('/{id}', [BookingController::class, 'destroy']);   // Delete booking
});
