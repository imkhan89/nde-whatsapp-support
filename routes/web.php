<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SupportController;
use App\Http\Controllers\ReplyController;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/support', [SupportController::class, 'index'])
    ->name('support.index');


Route::get('/support/{customer}', [SupportController::class, 'show'])
    ->name('support.show');


Route::post('/support/{customer}/reply', [ReplyController::class, 'send'])
    ->name('support.reply');


Route::post('/api/support/{customer}/reply', [ReplyController::class, 'sendAjax'])
    ->name('support.reply.ajax');