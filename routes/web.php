<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MvcController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\Gen\TemplateController;

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

// Handle the available languages with prefixed url's
// the locale gets set to the session in the Controller and then they get redirected to the regular URL
Route::get('/en/{one?}/{two?}/{three?}/{four?}/{five?}', [Controller::class, 'en'])->name('en');
Route::get('/es/{one?}/{two?}/{three?}/{four?}/{five?}', [Controller::class, 'es'])->name('es');
Route::get('/zh/{one?}/{two?}/{three?}/{four?}/{five?}', [Controller::class, 'zh'])->name('zh');

// Front Page
Route::get('/', [HomeController::class, 'frontpage'])->name('frontpage');
Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard')->middleware('auth');
Route::get('/sitemap', [HomeController::class, 'sitemap'])->name('sitemap');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/language/{locale}', [Controller::class, 'language']);

// Auth
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');

// MVC
Route::group(['prefix' => 'mvc'], function () {
	Route::get('/', [MvcController::class, 'index']);
	Route::get('/add/', [MvcController::class, 'add']);
	Route::post('/create/', [MvcController::class, 'create']);
	Route::get('/view/{model}/{views}', [MvcController::class, 'view']);
});

// Email
Route::group(['prefix' => 'email'], function () {
	Route::get('/send/{user}', [EmailController::class, 'send']);
});

// Templates
Route::group(['prefix' => 'templates'], function () {
	Route::get('/', [TemplateController::class, 'index']);
	Route::get('/index', [TemplateController::class, 'index']);
	Route::get('/view/{template}', [TemplateController::class, 'view']);

	// add
	Route::get('/add', [TemplateController::class, 'add']);
	Route::post('/create', [TemplateController::class, 'create']);
	
	// edit
	Route::get('/edit/{template}', [TemplateController::class, 'edit']);
	Route::post('/update/{template}', [TemplateController::class, 'update']);

	// delete
	Route::get('/confirmdelete/{template}', [TemplateController::class, 'confirmDelete']);
	Route::post('/delete/{template}', [TemplateController::class, 'delete']);
});

// Users
Route::group(['prefix' => 'users'], function () {
	Route::get('/', [UserController::class, 'index']);
	Route::get('/index', [UserController::class, 'index']);
	Route::get('/view/{user}', [UserController::class, 'view']);

	// add
	Route::get('/register', [RegisterController::class, 'register'])->name('register');
	Route::post('/create', [RegisterController::class, 'create']);
	
	// edit
	Route::get('/edit/{user}', [UserController::class, 'edit']);
	Route::post('/update/{user}', [UserController::class, 'update']);

	// delete
	Route::get('/confirmdelete/{user}', [UserController::class, 'confirmDelete']);
	Route::post('/delete/{user}', [UserController::class, 'delete']);
	
	// email verification
	Route::get('/verify-email/{user}/{token}', [VerificationController::class, 'verifyEmail']);
	
});

// Translations
Route::group(['prefix' => 'translations'], function () {
	// index
	Route::get('/', [TranslationController::class, 'index'])->name('translations')->middleware('auth');
	Route::get('/view/{filename}', [TranslationController::class, 'view']);

	// edit
	Route::get('/edit/{filename}',[TranslationController::class, 'edit']);
	Route::post('/update/{filename}',[TranslationController::class, 'update']);
});

// Password
Route::group(['prefix' => 'password'], function () {
	
	// reset via email
	Route::get('/request-reset', [ResetPasswordController::class, 'requestReset']);
	Route::post('/send-password-reset', [ResetPasswordController::class, 'sendPasswordReset']);
	Route::get('/reset/{user}/{token}', [ResetPasswordController::class, 'resetPassword']);
	Route::post('/reset-update/{user}', [ResetPasswordController::class, 'updatePassword']);
	
	// edit
	Route::get('/edit/{user}', [LoginController::class, 'editPassword']);
	Route::post('/update/{user}', [LoginController::class, 'updatePassword']);
});

// Events
Route::group(['prefix' => 'events'], function () {
	Route::get('/', [EventController::class, 'index'])->middleware('admin');
	Route::get('/confirmdelete', [EventController::class, 'confirmdelete'])->middleware('admin');
	Route::get('/delete/{filter?}', [EventController::class, 'delete'])->middleware('admin');

	// has to go last
	Route::get('/index/{filter?}', [EventController::class, 'index'])->middleware('admin');
});

// =================================================================
// The following groups and routes are generated
// Move permanent routes above this section
// =================================================================

// GENERATED for Visitor model
use App\Http\Controllers\Gen\VisitorController;
	
// Visitors
Route::group(['prefix' => 'visitors'], function () {
	Route::get('/', [VisitorController::class, 'index']);
	Route::get('/index', [VisitorController::class, 'index']);
	Route::get('/view/{visitor}', [VisitorController::class, 'view']);

	// add
	Route::get('/add', [VisitorController::class, 'add']);
	Route::post('/create', [VisitorController::class, 'create']);
	
	// edit
	Route::get('/edit/{visitor}', [VisitorController::class, 'edit']);
	Route::post('/update/{visitor}', [VisitorController::class, 'update']);

	// delete
	Route::get('/confirmdelete/{visitor}', [VisitorController::class, 'confirmDelete']);
	Route::post('/delete/{visitor}', [VisitorController::class, 'delete']);
});
