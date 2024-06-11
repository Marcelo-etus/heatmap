<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstagramController;

Route::post('/instagram-data', [InstagramController::class, 'getInstagramData']);

Route::get('/', function () {
    return view('welcome');
});

