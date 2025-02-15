<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\TranslationController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::resource('translations', TranslationController::class);
});