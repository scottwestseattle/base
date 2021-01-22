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

/*****************************************************************************
 How locale urls are handled
 1. All locale urls (/es/about) are routed through the first three routes below
 2. In the controller, the locale gets set to the session in the Controller
    and then they get redirected to the regular URLs whose routes are handled
    farther down
*****************************************************************************/

// Handle the available languages with prefixed url's
Route::get('/en/{one?}/{two?}/{three?}/{four?}/{five?}', [Controller::class, 'routeLocale']);
Route::get('/es/{one?}/{two?}/{three?}/{four?}/{five?}', [Controller::class, 'routeLocale']);
Route::get('/zh/{one?}/{two?}/{three?}/{four?}/{five?}', [Controller::class, 'routeLocale']);

///////////////////////////////////////////////////////////////////////////////
// Front Page
Route::get('/', [HomeController::class, 'frontpage'])->name('frontpage');
Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard')->middleware('auth');
Route::get('/language/{locale}', [Controller::class, 'language']);
Route::get('/sitemap', [HomeController::class, 'sitemap']);
Route::get('/about', [HomeController::class, 'about']);
Route::get('/terms', [HomeController::class, 'terms']);
Route::get('/privacy', [HomeController::class, 'privacy']);
Route::get('/contact', [HomeController::class, 'contact']);

// Auth
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');

// MVC
Route::group(['prefix' => 'mvc'], function () {
	Route::get('/', [MvcController::class, 'index']);
	Route::get('/add/', [MvcController::class, 'add']);
	Route::post('/create/', [MvcController::class, 'create']);
	Route::get('/view/{model}/{views}', [MvcController::class, 'view']);
	Route::get('/confirmdelete/{views}', [MvcController::class, 'confirmDelete']);
	Route::post('/delete', [MvcController::class, 'delete']);
});

// Email
Route::group(['prefix' => 'email'], function () {
	Route::get('/send/{user}', [EmailController::class, 'send']);
});

// Templates
Route::group(['prefix' => 'templates'], function () {
	Route::get('/', [TemplateController::class, 'index']);
	Route::get('/index', [TemplateController::class, 'index']);

	// add
	Route::get('/add', [TemplateController::class, 'add']);
	Route::post('/create', [TemplateController::class, 'create']);

	// edit
	Route::get('/edit/{template}', [TemplateController::class, 'edit']);
	Route::post('/update/{template}', [TemplateController::class, 'update']);

	// publish
	Route::get('/publish/{template}', [TemplateController::class, 'publish']);
	Route::post('/publishupdate/{template}', [TemplateController::class, 'updatePublish']);
	Route::get('/publishupdate/{template}', [TemplateController::class, 'updatePublish']);

	// delete
	Route::get('/confirmdelete/{template}', [TemplateController::class, 'confirmDelete']);
	Route::post('/delete/{template}', [TemplateController::class, 'delete']);
	Route::get('/delete/{template}', [TemplateController::class, 'delete']);

	// undelete
	Route::get('/deleted', [TemplateController::class, 'deleted']);
	Route::get('/undelete/{id}', [TemplateController::class, 'undelete']);

	// view
	Route::get('/view/{template}', [TemplateController::class, 'view']);
	Route::get('/{permalink}', [TemplateController::class, 'permalink']);
});

// Users
Route::group(['prefix' => 'users'], function () {
	Route::get('/', [UserController::class, 'index']);
	Route::get('/index', [UserController::class, 'index']);
	Route::get('/view/{user}', [UserController::class, 'view']);

	// add
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

// GENERATED for Comment model
use App\Http\Controllers\CommentController;

// Comments
Route::group(['prefix' => 'comments'], function () {
	Route::get('/', [CommentController::class, 'index']);
	Route::get('/index', [CommentController::class, 'index']);
	Route::get('/view/{comment}', [CommentController::class, 'view']);

	// add
	Route::get('/add', [CommentController::class, 'add']);
	Route::post('/create', [CommentController::class, 'create']);

	// edit
	Route::get('/edit/{comment}', [CommentController::class, 'edit']);
	Route::post('/update/{comment}', [CommentController::class, 'update']);

	// publish
	Route::get('/publish/{comment}', [CommentController::class, 'publish']);
	Route::post('/publishupdate/{comment}', [CommentController::class, 'updatePublish']);
	Route::get('/publishupdate/{comment}', [CommentController::class, 'updatePublish']);

	// delete
	Route::get('/confirmdelete/{comment}', [CommentController::class, 'confirmDelete']);
	Route::post('/delete/{comment}', [CommentController::class, 'delete']);
	Route::get('/delete/{comment}', [CommentController::class, 'delete']);

	// undelete
	Route::get('/deleted', [CommentController::class, 'deleted']);
	Route::get('/undelete/{id}', [CommentController::class, 'undelete']);
});
// GENERATED for Entry model
use App\Http\Controllers\EntryController;

// Entries
Route::group(['prefix' => 'entries'], function () {
	Route::get('/', [EntryController::class, 'index']);
	Route::get('/index', [EntryController::class, 'index']);

	// add
	Route::get('/add', [EntryController::class, 'add']);
	Route::post('/create', [EntryController::class, 'create']);

	// edit
	Route::get('/edit/{entry}', [EntryController::class, 'edit']);
	Route::post('/update/{entry}', [EntryController::class, 'update']);

	// publish
	Route::get('/publish/{entry}', [EntryController::class, 'publish']);
	Route::post('/publishupdate/{entry}', [EntryController::class, 'updatePublish']);
	Route::get('/publishupdate/{entry}', [EntryController::class, 'updatePublish']);

	// delete
	Route::get('/confirmdelete/{entry}', [EntryController::class, 'confirmDelete']);
	Route::post('/delete/{entry}', [EntryController::class, 'delete']);
	Route::get('/delete/{entry}', [EntryController::class, 'delete']);

	// undelete
	Route::get('/deleted', [EntryController::class, 'deleted']);
	Route::get('/undelete/{id}', [EntryController::class, 'undelete']);

	// view
	Route::get('/view/{entry}', [EntryController::class, 'view']);
	Route::get('/{permalink}', [EntryController::class, 'permalink']);

});
// GENERATED for Site model
use App\Http\Controllers\SiteController;

// Sites
Route::group(['prefix' => 'sites'], function () {
	Route::get('/', [SiteController::class, 'index']);
	Route::get('/index', [SiteController::class, 'index']);
	Route::get('/view/{site}', [SiteController::class, 'view']);

	// add
	Route::get('/add', [SiteController::class, 'add']);
	Route::post('/create', [SiteController::class, 'create']);

	// edit
	Route::get('/edit/{site}', [SiteController::class, 'edit']);
	Route::post('/update/{site}', [SiteController::class, 'update']);

	// publish
	Route::get('/publish/{site}', [SiteController::class, 'publish']);
	Route::post('/publishupdate/{site}', [SiteController::class, 'updatePublish']);
	Route::get('/publishupdate/{site}', [SiteController::class, 'updatePublish']);

	// delete
	Route::get('/confirmdelete/{site}', [SiteController::class, 'confirmDelete']);
	Route::post('/delete/{site}', [SiteController::class, 'delete']);
	Route::get('/delete/{site}', [SiteController::class, 'delete']);

	// undelete
	Route::get('/deleted', [SiteController::class, 'deleted']);
	Route::get('/undelete/{id}', [SiteController::class, 'undelete']);
});
