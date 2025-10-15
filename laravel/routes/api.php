<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaskesController;

Route::apiResource('faskes', FaskesController::class);
