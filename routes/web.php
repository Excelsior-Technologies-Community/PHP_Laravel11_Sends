<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SendMailController;

Route::get('/', [SendMailController::class, 'index']);
Route::post('/send-mail', [SendMailController::class, 'send']);
Route::get('/all-sends', [SendMailController::class, 'allSends']);

