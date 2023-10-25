<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DetSadController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\PostCommentsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [Controller::class, 'main']);

//Авторизация, регистрация
Route::get('/verification-message', function () {return view('users.verification', ['title' => 'Подтвердите свою почту',]);});
Route::get('/verification-notice', function () {return view('users.notice', ['title' => 'Почта уже была подтверждена',]);})->name('verification.notice');
Route::get('/profile', [UserController::class, 'profile'])->middleware('auth', 'verified');
Route::post('/update-profile', [UserController::class, 'updateProfile'])->middleware('auth', 'verified');
Route::get('/log-out', [LoginController::class, 'logout'])->name('log-out');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::get('/verification-success', function () {return view('users.verificationSuccess', ['title' => 'Регистрация подтверждена!',]);})->name('verification.verified');
Route::get('/agent', [UserController::class, 'agent'])->middleware('is.admin');
Auth::routes(['verify' => true]);

//Садики
Route::get('/{section_id}-{section_alias}', [DetSadController::class, 'section'])->where(['section_id' => '[0-9]+', 'section_alias' => '[a-z0-9-]+']);
Route::get('/{section_id}-{section_alias}/{category_id}-{category_alias}', [DetSadController::class, 'category'])->where(['section_id' => '[0-9]+','category_id' => '[0-9]+', 'section_alias' => '[a-z0-9-]+', 'category_alias' => '[a-z0-9-]+']);
Route::get('/{section_id}-{section_alias}/{category_id}-{category_alias}/{district}', [DetSadController::class, 'category'])->where(['section_id' => '[0-9]+','category_id' => '[0-9]+', 'section_alias' => '[a-z0-9-]+', 'category_alias' => '[a-z0-9-]+', 'district' => '[a-z][a-z0-9-]+']);
Route::get('/street/{category_id}-{category_alias}', [DetSadController::class, 'streets'])->where(['category_id' => '[0-9]+','category_alias' => '[a-z0-9-]+']);
Route::get('/street/{category_id}-{category_alias}/{street_alias}', [DetSadController::class, 'street'])->where(['category_id' => '[0-9]+','category_alias' => '[a-z0-9-]+', 'street_alias' => '[a-z0-9-]+']);
Route::get('/{section_id}-{section_alias}/{category_id}-{category_alias}/{sad_id}-{sad_alias}', [DetSadController::class, 'sadik'])->where(['category_id' => '[0-9]+', 'category_alias' => '[a-z0-9-]+', 'sad_id' => '[0-9]+', 'sad_alias' => '[a-z0-9-]+']);
Route::post('/detsad-post', [DetSadController::class, 'getResponse']);
Route::any('/add-photo', [DetSadController::class, 'getResponse']);
//Садики старые url
Route::middleware('redirect.old')->group(function () {
    Route::get('/detskie-sady/{section_id}-{section_alias}', [DetSadController::class, 'section'])->where(['section_id' => '[0-9]+', 'section_alias' => '[a-z0-9-]+']);
    Route::get('/detskie-sady/{section_id}-{section_alias}/{category_id}-{category_alias}', [DetSadController::class, 'category'])->where(['category_id' => '[0-9]+', 'category_alias' => '[a-z0-9-]+']);
    Route::get('/detskie-sady/{section_id}-{section_alias}/{category_id}-{category_alias}/{district}', [DetSadController::class, 'category'])->where(['section_id' => '[0-9]+','category_id' => '[0-9]+', 'section_alias' => '[a-z0-9-]+', 'category_alias' => '[a-z0-9-]+', 'district' => '[a-z][a-z0-9-]+']);
    Route::get('/detskie-sady/{section_id}-{section_alias}/{category_id}-{category_alias}/{sad_id}-{sad_alias}', [DetSadController::class, 'sadik'])->where(['category_id' => '[0-9]+', 'category_alias' => '[a-z0-9-]+', 'sad_id' => '[0-9]+', 'sad_alias' => '[a-z0-9-]+']);

    Route::get('/detskie-sady/{section_id}-{section_alias}/{category_id}-{category_alias}/{sad_id}-{sad_alias}/agent', [DetSadController::class, 'sadAgent'])->where(['category_id' => '[0-9]+', 'category_alias' => '[a-z0-9-]+', 'sad_id' => '[0-9]+', 'sad_alias' => '[a-z0-9-]+']);
    Route::get('/detskie-sady/{section_id}-{section_alias}/{category_id}-{category_alias}/{sad_id}-{sad_alias}/gallery', [DetSadController::class, 'gallery'])->where(['category_id' => '[0-9]+', 'category_alias' => '[a-z0-9-]+', 'sad_id' => '[0-9]+', 'sad_alias' => '[a-z0-9-]+']);
});

//Нашли ошибку
Route::any('/post/error', [ErrorController::class, 'getResponse']);
//Комменты
Route::post('/post/comment', [PostCommentsController::class, 'getResponse']);
