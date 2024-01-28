<?php

use App\Http\Controllers\AdminCommentsController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DetSadController;
use App\Http\Controllers\DiplomController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\ExchangeJobController;
use App\Http\Controllers\PostCommentsController;
use App\Http\Controllers\SiteMapController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

//Садики старые url
Route::prefix('detskie-sady')->group(function (){
    Route::redirect('/{section_id}-{section_alias}', '/{section_id}-{section_alias}');
    Route::redirect('/{section_id}-{section_alias}/{category_id}-{category_alias}', '/{section_id}-{section_alias}/{category_id}-{category_alias}');
    Route::redirect('/{section_id}-{section_alias}/{category_id}-{category_alias}/{district}', '/{section_id}-{section_alias}/{category_id}-{category_alias}/{district}');
    Route::redirect('/{section_id}-{section_alias}/{category_id}-{category_alias}/{sad_id}-{sad_alias}', '/{section_id}-{section_alias}/{category_id}-{category_alias}/{sad_id}-{sad_alias}');
    Route::redirect('/{section_id}-{section_alias}/{category_id}-{category_alias}/{sad_id}-{sad_alias}/gallery', '/{section_id}-{section_alias}/{category_id}-{category_alias}/{sad_id}-{sad_alias}/gallery');
    Route::redirect('/{section_id}-{section_alias}/{category_id}-{category_alias}/{sad_id}-{sad_alias}/agent', '/{section_id}-{section_alias}/{category_id}-{category_alias}/{sad_id}-{sad_alias}/agent');
    Route::redirect('/{section_id}-{section_alias}/{category_id}-{category_alias}/{sad_id}-{sad_alias}/geoshow', '/{section_id}-{section_alias}/{category_id}-{category_alias}/{sad_id}-{sad_alias}/geoshow');
});

Route::get('/', [Controller::class, 'main']);
//Прочие страницы и разделы
Route::get('/zanyatiya-v-detskom-sadu', [Controller::class, 'ClassesMain']);
Route::get('/zanyatiya/{category_id}-{category_alias}', [Controller::class, 'ClassesCategory'])->where(['category_id' => '[0-9]+', 'category_alias' => '[a-z0-9-]+']);
Route::get('/zanyatiya/{category_id}-{category_alias}/{id}-{alias}', [Controller::class, 'ClassesContent'])->where(['category_id' => '[0-9]+', 'category_alias' => '[a-z0-9-]+', 'id' => '[0-9]+', 'alias' => '[a-z0-9-]+']);
Route::get('/dobavit-sad', [Controller::class, 'addSad']);
Route::get('/svyaz', [Controller::class, 'contact']);

//SiteMap
Route::get('/sitemap', [SiteMapController::class, 'makeSiteMap'])->middleware('only.this.server');

//Метро
Route::get('/metro', [DetSadController::class, 'metroMain']);
Route::get('/metro/{category_id}-{category_alias}', [DetSadController::class, 'metroCategory'])->where(['category_id' => '[1-7]+', 'category_alias' => '[a-z0-9-]+']);

//Авторизация, регистрация
Route::get('/agent', [UserController::class, 'agent'])->middleware('is.admin');
Route::get('/verification-message', function () {return view('users.verification', ['title' => 'Подтвердите свою почту',]);});
Route::get('/verification-notice', function () {return view('users.notice', ['title' => 'Почта уже была подтверждена',]);})->name('verification.notice');
Route::get('/profile', [UserController::class, 'profile'])->middleware('auth', 'verified');
Route::post('/update-profile', [UserController::class, 'updateProfile'])->middleware('auth', 'verified');
Route::get('/log-out', [LoginController::class, 'logout'])->name('log-out');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify']);
Route::get('/verification-success', function () {return view('users.verificationSuccess', ['title' => 'Регистрация подтверждена!',]);})->name('verification.verified');
Auth::routes(['verify' => true]);

//Обмен местами в детских садах
Route::get('/obmen-add', [ExchangeJobController::class, 'addExchangeGet']);
Route::post('/obmen-add', [ExchangeJobController::class, 'addExchangePost']);
Route::prefix('obmen-mest')->group(function () {
    Route::get('/', [ExchangeJobController::class, 'exchange']);
    Route::get('/{city_id}-{city_alias}', [ExchangeJobController::class, 'exchange'])->where(['city_id' => '[0-9]+', 'city_alias' => '[a-z0-9-]+']);
    Route::get('/{city_id}-{city_alias}/{metro_id}-{metro_alias}', [ExchangeJobController::class, 'exchange'])->where(['city_id' => '[0-9]+', 'city_alias' => '[a-z0-9-]+','metro_id' => '[0-9]+', 'metro_alias' => '[a-z0-9-]+']);

});

//Работа в детских садах
Route::get('/rabota-add', [ExchangeJobController::class, 'addJobGet']);
Route::post('/rabota-add', [ExchangeJobController::class, 'addJobPost']);
Route::prefix('rabota')->group(function () {
    Route::get('/', [ExchangeJobController::class, 'job']);
    Route::get('/{city_id}-{city_alias}', [ExchangeJobController::class, 'job'])->where(['city_id' => '[0-9]+', 'city_alias' => '[a-z0-9-]+']);
    Route::get('/{city_id}-{city_alias}/{metro_id}-{metro_alias}', [ExchangeJobController::class, 'job'])->where(['city_id' => '[0-9]+', 'city_alias' => '[a-z0-9-]+','metro_id' => '[0-9]+', 'metro_alias' => '[a-z0-9-]+']);
    Route::get('/publish/{id}', [ExchangeJobController::class, 'publishJob'])->where(['id' => '[0-9]+'])->middleware('is.admin');
    Route::get('/delete/{id}', [ExchangeJobController::class, 'deleteJob'])->where(['id' => '[0-9]+'])->middleware('is.admin');
});

//Садики
Route::get('/{section_id}-{section_alias}', [DetSadController::class, 'section'])->where(['section_id' => '[0-9]+', 'section_alias' => '[a-z0-9-]+']);
Route::get('/{section_id}-{section_alias}/{category_id}-{category_alias}', [DetSadController::class, 'category'])->where(['section_id' => '[0-9]+','category_id' => '[0-9]+', 'section_alias' => '[a-z0-9-]+', 'category_alias' => '[a-z0-9-]+']);
Route::get('/{section_id}-{section_alias}/{category_id}-{category_alias}/{district}', [DetSadController::class, 'category'])->where(['section_id' => '[0-9]+','category_id' => '[0-9]+', 'section_alias' => '[a-z0-9-]+', 'category_alias' => '[a-z0-9-]+', 'district' => '[a-z][a-z0-9-]+']);
Route::get('/street/{category_id}-{category_alias}', [DetSadController::class, 'streets'])->where(['category_id' => '[0-9]+','category_alias' => '[a-z0-9-]+']);
Route::get('/street/{category_id}-{category_alias}/{street_alias}', [DetSadController::class, 'street'])->where(['category_id' => '[0-9]+','category_alias' => '[a-z0-9-]+', 'street_alias' => '[a-z0-9-]+']);
Route::get('/{section_id}-{section_alias}/{category_id}-{category_alias}/{sad_id}-{sad_alias}', [DetSadController::class, 'sadik'])->where(['category_id' => '[0-9]+', 'category_alias' => '[a-z0-9-]+', 'sad_id' => '[0-9]+', 'sad_alias' => '[a-z0-9-]+']);
Route::get('/{section_id}-{section_alias}/{category_id}-{category_alias}/{sad_id}-{sad_alias}/gallery', [DetSadController::class, 'gallery'])->where(['category_id' => '[0-9]+', 'category_alias' => '[a-z0-9-]+', 'sad_id' => '[0-9]+', 'sad_alias' => '[a-z0-9-]+', 'gallery' => 'gallery']);
Route::get('/gallery-add', [DetSadController::class, 'addGallery']);
Route::post('/post/add-gallery', [DetSadController::class, 'addGalleryPost']);
Route::get('/remove-image-gallery', [DetSadController::class, 'delImageGallery'])->middleware('admin.or.agent');
Route::get('/publish-image-gallery', [DetSadController::class, 'PublishImageGallery'])->middleware('is.admin');
Route::post('/detsad-post', [DetSadController::class, 'getTelephoneOrEmail']);
Route::any('/add-photo', [DetSadController::class, 'addPhotoMainPage']);
Route::get('/{section_id}-{section_alias}/{category_id}-{category_alias}/{sad_id}-{sad_alias}/agent', [DetSadController::class, 'sadAgent'])->where(['category_id' => '[0-9]+', 'category_alias' => '[a-z0-9-]+', 'sad_id' => '[0-9]+', 'sad_alias' => '[a-z0-9-]+']);
Route::get('/registration-agent', [DetSadController::class, 'registrationAgentGet']);
Route::post('/registration-agent', [RegisterController::class, 'register'])->name('registrationAgentPost');
Route::get('/{section_id}-{section_alias}/{category_id}-{category_alias}/{sad_id}-{sad_alias}/geoshow', [DetSadController::class, 'sadGeoShow'])->where(['category_id' => '[0-9]+', 'category_alias' => '[a-z0-9-]+', 'sad_id' => '[0-9]+', 'sad_alias' => '[a-z0-9-]+']);

//ajax запросы
Route::any('/ajax', [AjaxController::class, 'getResponse']);

//Нашли ошибку
Route::any('/post/error', [ErrorController::class, 'getResponse']);

//Комменты
Route::post('/post/comment', [PostCommentsController::class, 'getResponse']);
Route::get('/admin/comments', [AdminCommentsController::class, 'getResponse'])->middleware('is.admin');
Route::get('/comments', [AdminCommentsController::class, 'getResponse'])->middleware('is.admin');
//Диплом
Route::get('/diplom/code', [DiplomController::class, 'code']);
Route::get('/diplom/default', [DiplomController::class, 'default']);
