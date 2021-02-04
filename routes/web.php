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
use App\Http\Controllers\EntryController;

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
Route::get('/hash', [HomeController::class, 'hash']);
Route::post('/hash', [HomeController::class, 'hash']);

// Auth
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');

// Top level urls
Route::get('/articles', [EntryController::class, 'articles']);

// Articles
Route::group(['prefix' => 'articles'], function () {

    // add / (create done in entries)
	Route::get('/add/', [EntryController::class, 'addArticle']);

    // edit / (update done in entries)
	Route::get('/edit/', [EntryController::class, 'editArticle']);

    // permalink
	Route::get('/{permalink}', [EntryController::class, 'viewArticle']);
});

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

// Entries
Route::group(['prefix' => 'entries'], function () {
	Route::get('/', [EntryController::class, 'index']);
	Route::get('/index', [EntryController::class, 'index']);

	// view
	Route::get('/view/{entry}', [EntryController::class, 'view']);
	Route::get('/read/{entry}', [EntryController::class, 'read']);

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

	// permalink
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

// GENERATED for Word model
use App\Http\Controllers\WordController;

// Words
Route::group(['prefix' => 'words'], function () {
	Route::get('/', [WordController::class, 'index']);
	Route::get('/index', [WordController::class, 'index']);
    Route::get('/snippets', [DefinitionController::class, 'snippets']);

	// add
	Route::get('/add', [WordController::class, 'add']);
	Route::post('/create', [WordController::class, 'create']);

	// edit
	Route::get('/edit/{word}', [WordController::class, 'edit']);
	Route::post('/update/{word}', [WordController::class, 'update']);

	// publish
	Route::get('/publish/{word}', [WordController::class, 'publish']);
	Route::post('/publishupdate/{word}', [WordController::class, 'updatePublish']);
	Route::get('/publishupdate/{word}', [WordController::class, 'updatePublish']);

	// delete
	Route::get('/confirmdelete/{word}', [WordController::class, 'confirmDelete']);
	Route::post('/delete/{word}', [WordController::class, 'delete']);
	Route::get('/delete/{word}', [WordController::class, 'delete']);

	// undelete
	Route::get('/deleted', [WordController::class, 'deleted']);
	Route::get('/undelete/{id}', [WordController::class, 'undelete']);

	// view
	Route::get('/view/{word}', [WordController::class, 'view']);
	Route::get('/{permalink}', [WordController::class, 'permalink']);
});

// GENERATED for Tag model
use App\Http\Controllers\TagController;

// Tags
Route::group(['prefix' => 'tags'], function () {
	Route::get('/', [TagController::class, 'index']);
	Route::get('/index', [TagController::class, 'index']);

	// add
	Route::get('/add', [TagController::class, 'add']);
	Route::post('/create', [TagController::class, 'create']);

	// edit
	Route::get('/edit/{tag}', [TagController::class, 'edit']);
	Route::post('/update/{tag}', [TagController::class, 'update']);

	// publish
	Route::get('/publish/{tag}', [TagController::class, 'publish']);
	Route::post('/publishupdate/{tag}', [TagController::class, 'updatePublish']);
	Route::get('/publishupdate/{tag}', [TagController::class, 'updatePublish']);

	// delete
	Route::get('/confirmdelete/{tag}', [TagController::class, 'confirmDelete']);
	Route::post('/delete/{tag}', [TagController::class, 'delete']);
	Route::get('/delete/{tag}', [TagController::class, 'delete']);

	// undelete
	Route::get('/deleted', [TagController::class, 'deleted']);
	Route::get('/undelete/{id}', [TagController::class, 'undelete']);

	// view
	Route::get('/view/{tag}', [TagController::class, 'view']);
});

// GENERATED for Visitor model
use App\Http\Controllers\VisitorController;

// Visitors
Route::group(['prefix' => 'visitors'], function () {
	Route::get('/', [VisitorController::class, 'index']);
	Route::get('/index', [VisitorController::class, 'index']);

	// add
	Route::get('/add', [VisitorController::class, 'add']);
	Route::post('/create', [VisitorController::class, 'create']);

	// edit
	Route::get('/edit/{visitor}', [VisitorController::class, 'edit']);
	Route::post('/update/{visitor}', [VisitorController::class, 'update']);

	// publish
	Route::get('/publish/{visitor}', [VisitorController::class, 'publish']);
	Route::post('/publishupdate/{visitor}', [VisitorController::class, 'updatePublish']);
	Route::get('/publishupdate/{visitor}', [VisitorController::class, 'updatePublish']);

	// delete
	Route::get('/confirmdelete/{visitor}', [VisitorController::class, 'confirmDelete']);
	Route::post('/delete/{visitor}', [VisitorController::class, 'delete']);
	Route::get('/delete/{visitor}', [VisitorController::class, 'delete']);

	// undelete
	Route::get('/deleted', [VisitorController::class, 'deleted']);
	Route::get('/undelete/{id}', [VisitorController::class, 'undelete']);

	// view
	Route::get('/view/{entry}', [EntryController::class, 'view']);
	Route::get('/{permalink}', [EntryController::class, 'permalink']);
});

// GENERATED for Definition model
use App\Http\Controllers\Gen\DefinitionController;

Route::get('/dictionary', [DefinitionController::class, 'search']);
Route::get('/practice', [DefinitionController::class, 'snippets']);
Route::get('/favorites', [DefinitionController::class, 'favorites']);

// Definitions
Route::group(['prefix' => 'dictionary'], function () {
	Route::get('/search/{sort}', [DefinitionController::class, 'search']);
});

// Definitions
Route::group(['prefix' => 'definitions'], function () {
	Route::get('/', [DefinitionController::class, 'index']);
	Route::get('/index', [DefinitionController::class, 'index']);

	// add
	Route::get('/add/{word?}', [DefinitionController::class, 'add']);
	Route::post('/create', [DefinitionController::class, 'create']);

	// edit
	Route::get('/edit/{definition}', [DefinitionController::class, 'edit']);
	Route::post('/update/{definition}', [DefinitionController::class, 'update']);

	// publish
	Route::get('/publish/{definition}', [DefinitionController::class, 'publish']);
	Route::post('/publishupdate/{definition}', [DefinitionController::class, 'updatePublish']);
	Route::get('/publishupdate/{definition}', [DefinitionController::class, 'updatePublish']);

	// delete
	Route::get('/confirmdelete/{definition}', [DefinitionController::class, 'confirmDelete']);
	Route::post('/delete/{definition}', [DefinitionController::class, 'delete']);
	Route::get('/delete/{definition}', [DefinitionController::class, 'delete']);

	// undelete
	Route::get('/deleted', [DefinitionController::class, 'deleted']);
	Route::get('/undelete/{id}', [DefinitionController::class, 'undelete']);

	// view
	Route::get('/view/{definition}', [DefinitionController::class, 'view']);

	// custom
	Route::post('/create-snippet', [DefinitionController::class, 'createSnippet']);
	Route::get('/list/{tag}', [DefinitionController::class, 'list']);
	Route::get('/set-favorite-list/{definition}/{tagFromId}/{tagToId}',[DefinitionController::class, 'setFavoriteList']);
	Route::get('/review/{tag}/{reviewType?}', [DefinitionController::class, 'review']);
	Route::get('/review-newest/{reviewType?}', [DefinitionController::class, 'reviewNewest']);
	Route::get('/review-newest-verbs/{reviewType?}', [DefinitionController::class, 'reviewNewestVerbs']);
	Route::get('/review-random-words/{reviewType?}', [DefinitionController::class, 'reviewRandomWords']);
	Route::get('/review-random-verbs/{reviewType?}', [DefinitionController::class, 'reviewRandomVerbs']);

	// ajax calls
	Route::get('/find/{text}', [DefinitionController::class, 'find']);
	Route::get('/wordexists/{text}', [DefinitionController::class, 'wordExistsAjax']);
	Route::get('/get/{text}/{entryId}',[DefinitionController::class, 'getAjax']);
	Route::get('/translate/{text}/{entryId?}',[DefinitionController::class, 'translateAjax']);
	Route::get('/heart/{definition}',[DefinitionController::class, 'heartAjax']);
	Route::get('/unheart/{definition}',[DefinitionController::class, 'unheartAjax']);
	Route::get('/toggle-wip/{definition}',[DefinitionController::class, 'toggleWipAjax']);
	Route::get('/get-random-word/',[DefinitionController::class, 'getRandomWordAjax']);
	Route::get('/scrape-definition/{word}',[DefinitionController::class, 'scrapeDefinitionAjax']);

	// search
	Route::get('/search/{sort?}', [DefinitionController::class, 'search']);
	Route::post('/search/{sort?}', [DefinitionController::class, 'search']);
	Route::get('/search-ajax/{text?}', [DefinitionController::class, 'searchAjax']);

	// conjugations
	Route::get('/conjugationsgen/{definition}', [DefinitionController::class, 'conjugationsGen']);
	Route::get('/conjugationsgenajax/{text}', [DefinitionController::class, 'conjugationsGenAjax']);
	Route::get('/conjugationscomponent/{definition}',[DefinitionController::class, 'conjugationsComponentAjax']);

	//
	// catch-all last
	//
	Route::get('/{permalink}', [EntryController::class, 'permalink']);

});
