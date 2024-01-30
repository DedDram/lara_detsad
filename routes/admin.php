<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'admin',
], function () {
    Route::get('/test',  [ApiController::class, 'test']);
});
