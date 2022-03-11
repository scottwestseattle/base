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
use App\Http\Controllers\SiteController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\Gen\TemplateController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\Gen\ArticleController;

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
Route::get('/about', [HomeController::class, 'about']);
Route::get('/terms', [HomeController::class, 'terms']);
Route::get('/privacy', [HomeController::class, 'privacy']);
Route::get('/contact', [HomeController::class, 'contact']);
Route::get('/hash', [HomeController::class, 'hash']);
Route::post('/hash', [HomeController::class, 'hash']);
Route::get('/sitemap', [SiteController::class, 'sitemap']);

// Auth
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');

// Search
Route::get('/search', [HomeController::class, 'search']);
Route::post('/search', [HomeController::class, 'search']);

// Global
Route::get('/setlanguage/{languageId}', [Controller::class, 'setLanguage']);

Route::get('/clear-cache', function() {
    Cache::flush();
    return "Cache is cleared";
});

Route::get('/clear-view', function() {
    Artisan::call('view:clear');
    return "Compiled views cleared";
});

// Articles
Route::group(['prefix' => 'articles'], function () {

    // index
    Route::get('/', [ArticleController::class, 'index']);
    Route::get('/index/{orderBy}', [ArticleController::class, 'index']);
	Route::get('/view/{permalink}', [ArticleController::class, 'permalink']);
	Route::get('/show/{entry}', [ArticleController::class, 'view']);

    // add / (create done in entries)
	Route::get('/add/', [ArticleController::class, 'add']);
	Route::post('/create/', [ArticleController::class, 'create']);
	Route::get('/read/{entry}', [ArticleController::class, 'read']);

    // edit / update
	Route::get('/edit/{entry}', [ArticleController::class, 'edit']);
	Route::post('/update/{entry}', [ArticleController::class, 'update']);

    // confirm delete / delte
	Route::get('/confirmdelete/{entry}', [ArticleController::class, 'confirmDelete']);
	Route::post('/delete/{entry}', [ArticleController::class, 'delete']);
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
	Route::get('/deleted', [UserController::class, 'deleted']);
	Route::get('/undelete/{user}', [UserController::class, 'undelete']);

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

    // index
	Route::get('/', [EntryController::class, 'index']);
	Route::get('/index', [EntryController::class, 'index']);

	// stats
	Route::get('/stats/{entry}', [EntryController::class, 'stats']);
	Route::get('/superstats', [EntryController::class, 'superstats']);

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

    // custom
    Route::get('/set-read-location/{entry}/{location}', [EntryController::class, 'setReadLocationAjax']);
	Route::get('/get-definitions-user/{entry}', [EntryController::class, 'getDefinitionsUserAjax']);
	Route::get('/remove-definition-user-ajax/{entry}/{defId}', [EntryController::class, 'removeDefinitionUserAjax']);
	Route::get('/remove-definition-user/{entry}/{defId}', [EntryController::class, 'removeDefinitionUser']);

	// permalink
	Route::get('/{permalink}', [EntryController::class, 'permalink']);
});

// GENERATED for Site model

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

    // custom
	Route::get('/add-user-favorite-list', [TagController::class, 'addUserFavoriteList']);
	Route::post('/create-user-favorite-list', [TagController::class, 'createUserFavoriteList']);
	Route::get('/confirm-user-favorite-list-delete/{tag}', [TagController::class, 'confirmUserFavoriteListDelete']);
	Route::get('/edit-user-favorite-list/{tag}', [TagController::class, 'editUserFavoriteList']);

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

// Favorites lists
Route::group(['prefix' => 'favorites'], function () {
    Route::get('/', [DefinitionController::class, 'favorites']);
    Route::get('/rss', [DefinitionController::class, 'favoritesRss']);
    Route::get('/rss-reader/{tag}', [DefinitionController::class, 'favoritesRssReader']);
});

// Practice text (Snippets)
Route::group(['prefix' => 'practice'], function () {
    Route::get('/index/{count?}', [DefinitionController::class, 'indexSnippets']);
    Route::get('/view/{permalink}', [DefinitionController::class, 'viewSnippet']);
    Route::get('/show/{definition}', [DefinitionController::class, 'showSnippet']);
    Route::get('/edit/{definition}', [DefinitionController::class, 'editSnippet']);
    Route::post('/update/{definition}', [DefinitionController::class, 'updateSnippet']);
	Route::get('/read/{count?}', [DefinitionController::class, 'readSnippets']);
	Route::get('/cookie/{id}', [DefinitionController::class, 'setSnippetCookie']);
	Route::get('/filter/{parms}', [DefinitionController::class, 'filterSnippets']);
    Route::get('/{id?}', [DefinitionController::class, 'snippets']);
});

// Dictionary
Route::group(['prefix' => 'dictionary'], function () {
	Route::get('/search/{sort}', [DefinitionController::class, 'search']);
});

// Snippets
Route::group(['prefix' => 'snippets'], function () {
	Route::get('/read-latest/{count?}', [DefinitionController::class, 'readSnippetsLatest']);
	Route::get('/read/{count?}', [DefinitionController::class, 'readSnippets']);
	Route::get('/review/{reviewType?}/{count?}', [DefinitionController::class, 'reviewSnippets']);
	Route::get('/cookie/{id}', [DefinitionController::class, 'setSnippetCookie']);
});

// Verbs
Route::get('/verbs/conjugation/{verb}', [DefinitionController::class, 'verbs']);
Route::get('/dictionary/definition/{word}', [DefinitionController::class, 'display']);

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
	Route::get('/show/{definition}', [DefinitionController::class, 'view']);
	Route::get('/view/{permalink}', [DefinitionController::class, 'permalink']);

	// custom
	Route::post('/create-snippet', [DefinitionController::class, 'createSnippet']);
	Route::get('/list-tag/{tag}', [DefinitionController::class, 'listTag']);
	Route::get('/read-list/{tag}', [DefinitionController::class, 'readList']);
	Route::get('/read-random-words/{count?}', [DefinitionController::class, 'readRandomWords']);
	Route::get('/set-favorite-list/{definition}/{tagFromId}/{tagToId}',[DefinitionController::class, 'setFavoriteList']);
	Route::get('/review/{tag}/{reviewType?}', [DefinitionController::class, 'review']);
	Route::get('/review-newest/{reviewType?}/{count?}', [DefinitionController::class, 'reviewNewest']);
	Route::get('/review-newest-verbs/{reviewType?}/{count?}', [DefinitionController::class, 'reviewNewestVerbs']);
	Route::get('/review-random-words/{reviewType?}/{count?}', [DefinitionController::class, 'reviewRandomWords']);
	Route::get('/review-random-verbs/{reviewType?}/{count?}', [DefinitionController::class, 'reviewRandomVerbs']);
	Route::get('/review-top-20-verbs/{reviewType?}/{count?}', [DefinitionController::class, 'reviewRankedVerbs']);
	Route::get('/read-examples/{parms?}', [DefinitionController::class, 'readExamples']);


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

});

// GENERATED for Book model
use App\Http\Controllers\Gen\BookController;

// Books
Route::group(['prefix' => 'books'], function () {
	Route::get('/', [BookController::class, 'index']);
	Route::get('/admin', [BookController::class, 'admin']);
	Route::get('/index', [BookController::class, 'index']);

	// view
	Route::get('/view/{entry}', [BookController::class, 'view']);
	Route::get('/show/{permalink}', [BookController::class, 'permalink']);
	Route::get('/chapters/{tag}', [BookController::class, 'chapters']);

	// read
	Route::get('/read/{entry}', [BookController::class, 'read']);
	Route::get('/read-book/{tag}', [BookController::class, 'readBook']);

	// add
	Route::get('/add', [BookController::class, 'add']);
	Route::get('/add-chapter/{tag}', [BookController::class, 'addChapter']);
	Route::post('/create', [BookController::class, 'create']);

	// edit
	Route::get('/edit/{entry}', [BookController::class, 'edit']);
	Route::post('/update/{entry}', [BookController::class, 'update']);

	// publish
	Route::get('/publish/{entry}', [BookController::class, 'publish']);
	Route::post('/publishupdate/{entry}', [BookController::class, 'updatePublish']);
	Route::get('/publishupdate/{entry}', [BookController::class, 'updatePublish']);

	// delete
	Route::get('/confirmdelete/{entry}', [BookController::class, 'confirmDelete']);
	Route::post('/delete/{entry}', [BookController::class, 'delete']);
	Route::get('/delete/{entry}', [BookController::class, 'delete']);

	// undelete
	Route::get('/deleted', [BookController::class, 'deleted']);
	Route::get('/undelete/{id}', [BookController::class, 'undelete']);

});

// GENERATED for Course model
use App\Http\Controllers\Gen\CourseController;

// Courses
Route::group(['prefix' => 'courses'], function () {
	Route::get('/', [CourseController::class, 'index']);
	Route::get('/admin', [CourseController::class, 'admin']);
	Route::get('/index', [CourseController::class, 'index']);

	Route::get('/view/{course}', [CourseController::class, 'view']);
	Route::get('/rss', [CourseController::class, 'rss']);
	Route::get('/rss-reader', [CourseController::class, 'rssReader']);
	Route::get('/start', [CourseController::class, 'start']);

	// view
	//Route::get('/view/{permalink}', [CourseController::class, 'permalink']);
	Route::get('/show/{course}', [CourseController::class, 'view']);

	// add
	Route::get('/add', [CourseController::class, 'add']);
	Route::post('/create', [CourseController::class, 'create']);

	// edit
	Route::get('/edit/{course}', [CourseController::class, 'edit']);
	Route::post('/update/{course}', [CourseController::class, 'update']);

	// publish
	Route::get('/publish/{course}', [CourseController::class, 'publish']);
	Route::post('/publishupdate/{course}', [CourseController::class, 'updatePublish']);
	Route::get('/publishupdate/{course}', [CourseController::class, 'updatePublish']);

	// delete
	Route::get('/confirmdelete/{course}', [CourseController::class, 'confirmDelete']);
	Route::post('/delete/{course}', [CourseController::class, 'delete']);
	Route::get('/delete/{course}', [CourseController::class, 'delete']);

	// undelete
	Route::get('/deleted', [CourseController::class, 'deleted']);
	Route::get('/undelete/{id}', [CourseController::class, 'undelete']);
});

// GENERATED for Lesson model
use App\Http\Controllers\Gen\LessonController;

// Lessons
Route::group(['prefix' => 'lessons'], function () {
	Route::get('/', [LessonController::class, 'index']);

	Route::get('/admin', [LessonController::class, 'admin']);
	Route::get('/view/{lesson}',[LessonController::class, 'view']);
	Route::post('/view/{lesson}',[LessonController::class, 'view']); // just in case they hit enter on the ajax form
	Route::get('/review-orig/{lesson}/{reviewType?}',[LessonController::class, 'reviewOrig']);
	Route::get('/reviewmc/{lesson}/{reviewType?}',[LessonController::class, 'reviewmc']);
	Route::get('/review/{lesson}/{reviewType?}/{count?}',[LessonController::class, 'review']);
	Route::get('/read/{lesson}',[LessonController::class, 'read']);
	Route::get('/log-quiz/{lessonId}/{score}', [LessonController::class, 'logQuiz']);
	Route::get('/start/{lesson}/', [LessonController::class, 'start']);
	Route::get('/rss/{lesson}/', [LessonController::class, 'rss']);
	Route::get('/rss-reader/{lesson}', [LessonController::class, 'rssReader']);

	// add/create
	Route::get('/add/{course?}',[LessonController::class, 'add']);
	Route::post('/create',[LessonController::class, 'create']);

	// edit/update
	Route::get('/edit/{lesson}',[LessonController::class, 'edit']);
	Route::post('/update/{lesson}',[LessonController::class, 'update']);
	Route::get('/edit2/{lesson}',[LessonController::class, 'edit2']);
	Route::post('/update2/{lesson}',[LessonController::class, 'update2']);

	// delete
	Route::get('/confirmdelete/{lesson}',[LessonController::class, 'confirmdelete']);
	Route::post('/delete/{lesson}',[LessonController::class, 'delete']);
	Route::get('/undelete/{id}', [LessonController::class, 'undelete']);

	// add/create
	Route::get('/publish/{lesson}',[LessonController::class, 'publish']);
	Route::post('/publishupdate/{lesson}',[LessonController::class, 'publishupdate']);

    // convert to list
	Route::get('/convert-to-list/{lesson}',[LessonController::class, 'convertToList']);

	// ajax
	Route::get('/finished/{lesson}',[LessonController::class, 'toggleFinished']);

    // catch all
	Route::get('/{parent_id}', [LessonController::class, 'index']);
});

// GENERATED for History model
use App\Http\Controllers\Gen\HistoryController;

// Histories
Route::group(['prefix' => 'history'], function () {
	Route::get('/', [HistoryController::class, 'index']);
	Route::get('/admin', [HistoryController::class, 'admin']);
	Route::get('/index', [HistoryController::class, 'index']);
    Route::get('/rss', [HistoryController::class, 'rss']);

	// view
	Route::get('/show/{history}', [HistoryController::class, 'view']);

	// add
	Route::get('/add', [HistoryController::class, 'add']);
	Route::get('/add-public/{programName}/{programId}/{sessionName}/{sessionId}/{seconds}', [HistoryController::class, 'addPublic']);

	Route::post('/create', [HistoryController::class, 'create']);

	// edit
	Route::get('/edit/{history}', [HistoryController::class, 'edit']);
	Route::post('/update/{history}', [HistoryController::class, 'update']);

	// delete
	Route::get('/confirmdelete/{history}', [HistoryController::class, 'confirmDelete']);
	Route::post('/delete/{history}', [HistoryController::class, 'delete']);
	Route::get('/delete/{history}', [HistoryController::class, 'delete']);

	// undelete
	Route::get('/deleted', [HistoryController::class, 'deleted']);
	Route::get('/undelete/{id}', [HistoryController::class, 'undelete']);
});
