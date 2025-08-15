<?php

use Illuminate\Support\Facades\Route;

// Simple test route to verify API is working
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'timestamp' => now(),
        'routes_loaded' => true
    ]);
});

// Test route with parameter
Route::get('/test/{id}', function ($id) {
    return response()->json([
        'message' => 'API parameter test working!',
        'id' => $id,
        'timestamp' => now()
    ]);
});
