<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ai', [\App\Http\Controllers\AiController::class, 'index'])->name('ai.index');
