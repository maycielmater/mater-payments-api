<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/payments', function () {
    return response()->file(public_path('payments/index.php'));
});