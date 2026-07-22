<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/support', [SupportController::class, 'index'])->name('support.index');

Route::get('/support/{customer}', [SupportController::class, 'show'])->name('support.show');