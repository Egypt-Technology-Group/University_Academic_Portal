<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return response()->json([
        'message' => 'Unauthenticated or session expired.',
        'error' => 'unauthenticated'
    ], 401);
})->name('login');
