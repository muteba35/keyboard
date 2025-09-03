<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiTestController;

Route::get('/test', [ApiTestController::class, 'index']);
