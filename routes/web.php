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

// Generated Entries
use App\Http\Controllers\Gen\ExerciseController;
use App\Http\Controllers\Gen\StatController;
use App\Http\Controllers\Gen\HistoryController;
use App\Http\Controllers\Gen\LessonController;
use App\Http\Controllers\Gen\CourseController;
use App\Http\Controllers\Gen\BookController;
use App\Http\Controllers\Gen\DefinitionController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\WordController;
use App\Http\Controllers\Gen\ContactController;

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

// OLD: works using redirect which Google Search Indexing doesn't like
// Handle the available languages with prefixed url's
//Route::get('/en/{one?}/{two?}/{three?}/{four?}/{five?}', [Controller::class, 'routeLocale']);
//Route::get('/es/{one?}/{two?}/{three?}/{four?}/{five?}', [Controller::class, 'routeLocale']);
//Route::get('/zh/{one?}/{two?}/{three?}/{four?}/{five?}', [Controller::class, 'routeLocale']);
////////////////////////////////////////////////////////////////////////////////////////////////////////

Route::get('/language/{locale}', [Controller::class, 'language'])->name('language');
Route::get('/', [HomeController::class, 'frontpage'])->name('frontpage');

// Global
Route::get('/setlanguage/{languageId}', [Controller::class, 'setLanguage']);
Route::get('/setuserlevel/{userLevel}', [Controller::class, 'setUserLevel']);
Route::get('/set-session/', [Controller::class, 'setSession']);
Route::get('/sitemap', [HomeController::class, 'sitemap']);
Route::get('/test', [HomeController::class, 'test']);
Route::get('/d-e-b-u-g', [HomeController::class, 'debug']);
Route::get('/search-ajax/{text}/{searchType?}', [HomeController::class, 'searchAjax']);
Route::get('/c', [ContactController::class, 'index']);

Route::get('/clear-cache', function() {
    Cache::flush();
    return "Cache is cleared";
});

Route::get('/clear-view', function() {
    Artisan::call('view:clear');
    return "Compiled views cleared";
});

Route::get('/clear-sessions', function () {
    Session::flush();
    return "Sessions cleared";
})->name('flush');

Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');

// articles AJAX
Route::group(['prefix' => 'articles'], function () {
	Route::get('/flashcards/view/{entry}', [ArticleController::class, 'flashcardsView']); // for ajax: format flashcards to view
});

// definitions AJAX
Route::group(['prefix' => 'definitions'], function () {
	Route::get('/toggle-wip/{definition}',[DefinitionController::class, 'toggleWipAjax']);
	Route::get('/wordexists/{text}', [DefinitionController::class, 'wordExistsAjax']);
	Route::get('/find/{text}', [DefinitionController::class, 'find'])->name('definitions.find');
});

// history AJAX
Route::group(['prefix' => 'history'], function () {
    Route::get('/rss', [HistoryController::class, 'rss']);
	Route::get('/add-public/', [HistoryController::class, 'addPublic']);
});

// stats AJAX
Route::group(['prefix' => 'stats'], function () {
	Route::get('/update-stats', [StatController::class, 'updateStats']);
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

// MVC
Route::group(['prefix' => 'mvc'], function () {
	Route::get('/', [MvcController::class, 'index']);
	Route::get('/add/', [MvcController::class, 'add']);
	Route::post('/create/', [MvcController::class, 'create']);
	Route::get('/view/{model}/{views}', [MvcController::class, 'view']);
	Route::get('/confirmdelete/{views}', [MvcController::class, 'confirmDelete']);
	Route::post('/delete', [MvcController::class, 'delete']);
});

////////////////////////////////////////////////////////////////////////////////////////////////////////
// This is the Locale Prefix Handler; ex: name.com/es/artles << changes UI to ES; not CONTENT
////////////////////////////////////////////////////////////////////////////////////////////////////////
//todo:locale
Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => 'en|es|zh']
], function() {

///////////////////////////////////////////////////////////////////////////////
// Front Page
Route::get('/', [HomeController::class, 'frontpage'])->name('home.frontpage');
Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard')->middleware('auth');
Route::get('/about', [HomeController::class, 'about']);
Route::get('/terms', [HomeController::class, 'terms']);
Route::get('/privacy', [HomeController::class, 'privacy']);
Route::get('/contact', [HomeController::class, 'contact']);
Route::get('/hash', [HomeController::class, 'hash']);
Route::post('/hash', [HomeController::class, 'hash']);
Route::get('/sites/sitemap', [SiteController::class, 'sitemap']);

// Auth
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'register'])->name('register');

// Search
Route::get('/search', [HomeController::class, 'search']);
Route::post('/search', [HomeController::class, 'search']);

// Articles
Route::group(['prefix' => 'articles'], function () {
    // index
    Route::get('/', [ArticleController::class, 'index'])->name('articles');
    Route::get('/index/', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/view/{permalink}', [ArticleController::class, 'permalink'])->name('articles.view');
    Route::get('/show/{entry}', [ArticleController::class, 'view']);

    // add / (create done in entries)
	Route::get('/add/', [ArticleController::class, 'add'])->name('articles.add');
	Route::post('/create/', [ArticleController::class, 'create'])->name('articles.create');

    // read / flashcards
	Route::get('/read/{entry}', [ArticleController::class, 'read'])->name('articles.read');
	Route::get('/flashcards/{entry}/{count?}', [ArticleController::class, 'flashcards'])->name('articles.flashcards');
	Route::get('/quiz/{entry}/{qnaType}', [ArticleController::class, 'quiz'])->name('articles.quiz');

    // edit / update
	Route::get('/edit/{entry}', [ArticleController::class, 'edit'])->name('articles.edit');
	Route::post('/update/{entry}', [ArticleController::class, 'update'])->name('articles.update');

    // confirm delete / delte
	Route::get('/confirmdelete/{entry}', [ArticleController::class, 'confirmDelete'])->name('articles.confirmdelete');
	Route::post('/delete/{entry}', [ArticleController::class, 'delete'])->name('articles.delete');
	Route::get('/delete/{entry}', [ArticleController::class, 'delete'])->name('articles.deleteGet');

	// publish
	Route::get('/publishupdate/{entry}', [ArticleController::class, 'updatePublish'])->name('articles.publishUpdateGet'); // for ajax
	Route::post('/publishupdate/{entry}', [ArticleController::class, 'updatePublish'])->name('articles.publishUpdate');
	Route::get('/publish/{entry}', [ArticleController::class, 'publish'])->name('articles.publish');
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
	Route::get('/', [UserController::class, 'index'])->name('users');
	Route::get('/index', [UserController::class, 'index'])->name('users.index');
	Route::get('/view/{user}', [UserController::class, 'view'])->name('users.view');

	// add
	Route::post('/create', [RegisterController::class, 'create']);

	// edit
	Route::get('/edit/{user}', [UserController::class, 'edit'])->name('users.edit');
	Route::post('/update/{user}', [UserController::class, 'update'])->name('users.update');

	// delete
	Route::get('/confirmdelete/{user}', [UserController::class, 'confirmDelete']);
	Route::post('/delete/{user}', [UserController::class, 'delete']);
	Route::get('/delete/{user}', [UserController::class, 'delete']);
	Route::get('/deleted', [UserController::class, 'deleted']);
	Route::get('/undelete/{user}', [UserController::class, 'undelete']);

	// email verification
	Route::get('/verify-email/{user}/{token}', [VerificationController::class, 'verifyEmail']);

});

// Password
Route::group(['prefix' => 'password'], function () {

	// reset via email
	Route::get('/request-reset', [ResetPasswordController::class, 'requestReset'])->name('password.requestReset');
	Route::post('/send-password-reset', [ResetPasswordController::class, 'sendPasswordReset']);
	Route::get('/reset/{user}/{token}', [ResetPasswordController::class, 'resetPassword']);
	Route::post('/reset-update/{user}', [ResetPasswordController::class, 'updatePassword']);

	// edit
	Route::get('/edit/{user}', [LoginController::class, 'editPassword'])->name('password.edit');
	Route::post('/update/{user}', [LoginController::class, 'updatePassword'])->name('password.update');
});

// Events
Route::group(['prefix' => 'events'], function () {
	Route::get('/', [EventController::class, 'index'])->middleware('admin')->name('events');
	Route::get('/confirmdelete', [EventController::class, 'confirmdelete'])->middleware('admin')->name('events.confirmDelete');
	Route::get('/delete/{filter?}', [EventController::class, 'delete'])->middleware('admin')->name('events.delete');

	// has to go last
	Route::get('/index/{filter?}', [EventController::class, 'index'])->middleware('admin')->name('events.index');
});


// =================================================================
// The following groups and routes are generated
// Move permanent routes above this section
// =================================================================

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
	Route::get('/stats/{entry}', [EntryController::class, 'stats'])->name('entries.stats');
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
	Route::post('/delete/{entry}', [EntryController::class, 'delete'])->name('entries.delete');
	Route::get('/delete/{entry}', [EntryController::class, 'delete'])->name('entries.deleteGet');

	// undelete
	Route::get('/deleted', [EntryController::class, 'deleted']);
	Route::get('/undelete/{id}', [EntryController::class, 'undelete']);

    // custom
    Route::get('/set-read-location/{entry}/{location}', [EntryController::class, 'setReadLocationAjax']);
	Route::get('/get-definitions-user/{entry}', [EntryController::class, 'getDefinitionsUserAjax']);
	Route::get('/remove-definition-user-ajax/{entry}/{defId}', [EntryController::class, 'removeDefinitionUserAjax']);
	Route::get('/remove-definition-user/{entry}/{defId}', [EntryController::class, 'removeDefinitionUser']);

	// permalink
	Route::get('/{permalink}', [EntryController::class, 'permalink'])->name('entries.permalink');
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

// Tags
Route::group(['prefix' => 'tags'], function () {
	Route::get('/', [TagController::class, 'index'])->name('tags');
	Route::get('/index', [TagController::class, 'index'])->name('tags.index');

	// add
	Route::get('/add', [TagController::class, 'add'])->name('tags.add');
	Route::post('/create', [TagController::class, 'create'])->name('tags.create');

	// edit
	Route::get('/edit/{tag}', [TagController::class, 'edit'])->name('tags.edit');
	Route::post('/update/{tag}', [TagController::class, 'update'])->name('tags.update');

	// publish
	Route::get('/publish/{tag}', [TagController::class, 'publish'])->name('tags.publish');
	Route::post('/publishupdate/{tag}', [TagController::class, 'updatePublish'])->name('tags.publishUpdate');
	Route::get('/publishupdate/{tag}', [TagController::class, 'updatePublish']);

	// delete
	Route::get('/confirmdelete/{tag}', [TagController::class, 'confirmDelete'])->name('tags.confirmDelete');
	Route::post('/delete/{tag}', [TagController::class, 'delete'])->name('tags.delete');
	Route::get('/delete/{tag}', [TagController::class, 'delete']);

	// undelete
	Route::get('/deleted', [TagController::class, 'deleted'])->name('tags.deleted');
	Route::get('/undelete/{id}', [TagController::class, 'undelete'])->name('tags.undelete');

	// view
	Route::get('/view/{tag}', [TagController::class, 'view'])->name('tags.view');

    // custom
	Route::get('/add-user-favorite-list', [TagController::class, 'addUserFavoriteList'])->name('tags.addUserFavoriteList');
	Route::post('/create-user-favorite-list', [TagController::class, 'createUserFavoriteList'])->name('tags.createUserFavoriteList');
	Route::get('/confirm-user-favorite-list-delete/{tag}', [TagController::class, 'confirmUserFavoriteListDelete'])->name('tags.confirmUserFavoriteListDelete');
	Route::get('/edit-user-favorite-list/{tag}', [TagController::class, 'editUserFavoriteList'])->name('tags.editUserFavoriteList');
});

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

// Favorites lists
Route::group(['prefix' => 'favorites'], function () {
    Route::get('/', [DefinitionController::class, 'favorites'])->name('favorites');
    Route::get('/rss', [DefinitionController::class, 'favoritesRss'])->name('favorites.rss');
    Route::get('/rss-reader/{tag}', [DefinitionController::class, 'favoritesRssReader'])->name('favorites.rssReader');
});

// Practice text (Snippets)
Route::group(['prefix' => 'practice'], function () {
    Route::get('/index/', [DefinitionController::class, 'indexSnippets'])->name('practice.index');
    Route::get('/view/{permalink}', [DefinitionController::class, 'viewSnippet']);
    Route::get('/show/{definition}', [DefinitionController::class, 'showSnippet']);
    Route::get('/edit/{definition}', [DefinitionController::class, 'editSnippet']);
    Route::post('/update/{definition}', [DefinitionController::class, 'updateSnippet']);
	Route::get('/cookie/{id}', [DefinitionController::class, 'setSnippetCookie']);
	Route::get('/filter/{parms}', [DefinitionController::class, 'filterSnippets']);
    Route::get('/{id?}', [DefinitionController::class, 'snippets']);
    Route::get('/', [DefinitionController::class, 'indexSnippets'])->name('practice');
});

// Dictionary
Route::group(['prefix' => 'dictionary'], function () {
    Route::get('/', [DefinitionController::class, 'search'])->name('dictionary');
	Route::get('/search/{sort}', [DefinitionController::class, 'search'])->name('dictionary.search');
	Route::get('/create-quick/{text}', [DefinitionController::class, 'createQuick'])->name('dictionary.createQuickGet');
	Route::post('/create-quick', [DefinitionController::class, 'createQuick'])->name('dictionary.createQuick');
});

// Snippets
Route::group(['prefix' => 'snippets'], function () {
	Route::get('/read', [DefinitionController::class, 'readSnippets'])->name('snippets.read');
	Route::get('/cookie/{id}', [DefinitionController::class, 'setSnippetCookie']);

	// flashcards / quiz have two routes
	Route::get('/review', [DefinitionController::class, 'reviewSnippets'])->name('snippets.review');
	Route::get('/flashcards', [DefinitionController::class, 'snippetsFlashcards']);
	Route::get('/quiz', [DefinitionController::class, 'snippetsQuiz']);
});

// Daily exercise links - made unique for history uniqueness
Route::group(['prefix' => 'daily'], function () {
	Route::get('/flashcards-newest', [DefinitionController::class, 'flashcardsNewest']);
	Route::get('/flashcards-attempts', [DefinitionController::class, 'reviewSnippets']);
	Route::get('/dictionary-newest', [DefinitionController::class, 'reviewNewest']);
	Route::get('/dictionary-attempts', [DefinitionController::class, 'reviewDictionary']);
});

// Verbs
Route::get('/verbs/conjugation/{verb}', [DefinitionController::class, 'verbs']);
Route::get('/dictionary/definition/{word}', [DefinitionController::class, 'display']);

// Definitions
Route::group(['prefix' => 'definitions'], function () {
	Route::get('/', [DefinitionController::class, 'index'])->name('definitions.index');
	Route::get('/index', [DefinitionController::class, 'index']);

	// add
	Route::post('/add', [DefinitionController::class, 'add'])->name('definitions.add');
	Route::get('/add', [DefinitionController::class, 'add'])->name('definitions.addGet');
	Route::get('/add/{word}', [DefinitionController::class, 'add'])->name('definitions.addWord');
	Route::post('/create', [DefinitionController::class, 'create'])->name('definitions.create');

	// edit
	Route::get('/edit/{definition}', [DefinitionController::class, 'edit'])->name('definitions.edit');
	Route::get('/edit-or-show/{definition}', [DefinitionController::class, 'editOrShow'])->name('definitions.editOrShow');
	Route::post('/update/{definition}', [DefinitionController::class, 'update'])->name('definitions.update');

	// publish
	Route::get('/publish/{definition}', [DefinitionController::class, 'publish'])->name('definitions.publish');
	Route::post('/publishupdate/{definition}', [DefinitionController::class, 'updatePublish'])->name('definitions.publishUpdate');
	Route::get('/publishupdate/{definition}', [DefinitionController::class, 'updatePublish'])->name('definitions.publishUpdate');

	// delete
	Route::get('/confirmdelete/{definition}', [DefinitionController::class, 'confirmDelete'])->name('definitions.confirmDelete');
	Route::post('/delete/{definition}', [DefinitionController::class, 'delete'])->name('definitions.delete');
	Route::get('/delete/{definition}', [DefinitionController::class, 'delete'])->name('definitions.deleteGet');

	// undelete
	Route::get('/deleted', [DefinitionController::class, 'deleted'])->name('definitions.deleted');
	Route::get('/undelete/{id}', [DefinitionController::class, 'undelete'])->name('definitions.undelete');

	// view
	Route::get('/show/{definition}', [DefinitionController::class, 'view'])->name('definitions.show');
	Route::get('/view/{permalink}', [DefinitionController::class, 'permalink'])->name('definitions.view');

	// custom
	Route::post('/create-snippet', [DefinitionController::class, 'createSnippet'])->name('definitions.createSnippet');
	Route::get('/list-tag/{tag}', [DefinitionController::class, 'listTag'])->name('definitions.listTag');
	Route::get('/read-list/{tag}', [DefinitionController::class, 'readList']);
	Route::get('/read-random-words/{count?}', [DefinitionController::class, 'readRandomWords']);
	Route::get('/set-favorite-list/{definition}/{tagFromId}/{tagToId}',[DefinitionController::class, 'setFavoriteList'])->name('definitions.setFavoriteList');
	Route::get('/read-examples', [DefinitionController::class, 'readExamples'])->name('definitions.readExamples');
	Route::get('/stats/{tag}', [DefinitionController::class, 'stats']);
	Route::get('/update-stats/{definition}', [DefinitionController::class, 'updateStats']);

    // quiz / flashcards
	Route::get('/review/{tag}/{reviewType?}', [DefinitionController::class, 'review'])->name('definitions.review');
	Route::get('/flashcards/{tag}', [DefinitionController::class, 'favoritesFlashcards']);
	Route::get('/quiz/{tag}', [DefinitionController::class, 'favoritesQuiz']);

	// actions on all favorites: read, qna, flashcards
	Route::get('/favorites-review', [DefinitionController::class, 'favoritesReview'])->name('definitions.favoritesReview');
	Route::get('/convert-text-to-favorites/{entry}', [DefinitionController::class, 'convertTextToFavorites'])->name('definitions.convertTextToFavorites');
	Route::post('/convert-text-to-favorites/{entry}', [DefinitionController::class, 'convertTextToFavorites'])->name('definitions.convertTextToFavoritesPost');
	Route::get('/convert-questions-to-snippets/{entry}', [DefinitionController::class, 'convertQuestionsToSnippets'])->name('definitions.convertQuestionsToSnippets');
	Route::post('/convert-questions-to-snippets/{entry}', [DefinitionController::class, 'convertQuestionsToSnippets'])->name('definitions.convertQuestionsToSnippetsPost');

	Route::get('/review-newest/{reviewType?}/{count?}', [DefinitionController::class, 'reviewNewest'])->name('definitions.reviewNewest');
	Route::get('/review-newest-verbs/{reviewType?}/{count?}', [DefinitionController::class, 'reviewNewestVerbs'])->name('definitions.reviewNewestVerbs');
	Route::get('/review-random-words/{reviewType?}/{count?}', [DefinitionController::class, 'reviewRandomWords'])->name('definitions.reviewRandomWords');
	Route::get('/review-random-verbs/{reviewType?}/{count?}', [DefinitionController::class, 'reviewRandomVerbs'])->name('definitions.reviewRandomVerbs');
	Route::get('/review-top-20-verbs/{reviewType?}/{count?}', [DefinitionController::class, 'reviewRankedVerbs'])->name('definitions.reviewTop20Verbs');

	// ajax calls
	Route::get('/get/{text}/{entryId}',[DefinitionController::class, 'getAjax']);
	Route::get('/translate/{text}/{entryId?}',[DefinitionController::class, 'translateAjax']);
	Route::get('/heart/{definition}',[DefinitionController::class, 'heartAjax']);
	Route::get('/unheart/{definition}',[DefinitionController::class, 'unheartAjax']);
	Route::get('/move-favorites/{tag}/{tagToId?}',[DefinitionController::class, 'moveFavorites'])->name('definitions.moveFavorites');
	Route::get('/get-random-word/',[DefinitionController::class, 'getRandomWordAjax']);
	Route::get('/scrape-definition/{word}',[DefinitionController::class, 'scrapeDefinitionAjax']);

	// search
	Route::get('/search/{sort?}', [DefinitionController::class, 'search']);
	Route::post('/search/{sort?}', [DefinitionController::class, 'search']);
	Route::get('/search-ajax/{resultsFormat}/{text?}', [DefinitionController::class, 'searchAjax']);

	// conjugations
	Route::get('/conjugationsgen/{definition}', [DefinitionController::class, 'conjugationsGen']);
	Route::get('/conjugationsgenajax/{text}', [DefinitionController::class, 'conjugationsGenAjax']);
	Route::get('/conjugationscomponent/{definition}',[DefinitionController::class, 'conjugationsComponentAjax']);
});

// Books
Route::group(['prefix' => 'books'], function () {
	Route::get('/', [BookController::class, 'index'])->name('books');
	Route::get('/admin', [BookController::class, 'admin'])->name('books.admin');
	Route::get('/index', [BookController::class, 'index'])->name('books.index');

	// view
	Route::get('/view/{entry}', [BookController::class, 'view'])->name('books.view');
	Route::get('/show/{permalink}', [BookController::class, 'permalink'])->name('books.show');
	Route::get('/chapters/{tag}', [BookController::class, 'chapters'])->name('books.chapters');

	// read
	Route::get('/read/{entry}', [BookController::class, 'read'])->name('books.read');
	Route::get('/read-book/{tag}', [BookController::class, 'readBook'])->name('books.readBook');

	// add
	Route::get('/add', [BookController::class, 'add'])->name('books.add');
	Route::get('/add-chapter/{tag}', [BookController::class, 'addChapter'])->name('books.addChapter');
	Route::post('/create', [BookController::class, 'create'])->name('books.create');

	// edit
	Route::get('/edit/{entry}', [BookController::class, 'edit'])->name('books.edit');
	Route::post('/update/{entry}', [BookController::class, 'update'])->name('books.update');

	// publish
	Route::get('/publish/{entry}', [BookController::class, 'publish'])->name('books.publish');
	Route::post('/publishupdate/{entry}', [BookController::class, 'updatePublish'])->name('books.updatePublish');
	Route::get('/publishupdate/{entry}', [BookController::class, 'updatePublish']); // for ajax: toggles publish

	// delete
	Route::get('/confirmdelete/{entry}', [BookController::class, 'confirmDelete'])->name('books.confirmDelete');
	Route::post('/delete/{entry}', [BookController::class, 'delete'])->name('books.delete');
	Route::get('/delete/{entry}', [BookController::class, 'delete']);

	// undelete
	Route::get('/deleted', [BookController::class, 'deleted']);
	Route::get('/undelete/{id}', [BookController::class, 'undelete']);

	// stats
	Route::get('/stats/{tag}', [BookController::class, 'stats'])->name('books.stats');

});

// Courses
Route::group(['prefix' => 'courses'], function () {
	Route::get('/', [CourseController::class, 'index'])->name('courses');
	Route::get('/admin', [CourseController::class, 'admin'])->name('courses.admin');
	Route::get('/index', [CourseController::class, 'index'])->name('courses.index');

	Route::get('/view/{course}', [CourseController::class, 'view'])->name('courses.view');
	Route::get('/rss', [CourseController::class, 'rss']);
	Route::get('/rss-reader', [CourseController::class, 'rssReader'])->name('courses.rssReader');
	Route::get('/start', [CourseController::class, 'start']);

	// view
	//Route::get('/view/{permalink}', [CourseController::class, 'permalink']);
	Route::get('/show/{course}', [CourseController::class, 'view'])->name('courses.show');

	// add
	Route::get('/add', [CourseController::class, 'add'])->name('courses.add');
	Route::post('/create', [CourseController::class, 'create'])->name('courses.create');

	// edit
	Route::get('/edit/{course}', [CourseController::class, 'edit'])->name('courses.edit');
	Route::post('/update/{course}', [CourseController::class, 'update'])->name('courses.update');

	// publish
	Route::get('/publish/{course}', [CourseController::class, 'publish'])->name('courses.publish');
	Route::post('/publishupdate/{course}', [CourseController::class, 'updatePublish'])->name('courses.update');
	Route::get('/publishupdate/{course}', [CourseController::class, 'updatePublish'])->name('courses.updateGet');

	// delete
	Route::get('/confirmdelete/{course}', [CourseController::class, 'confirmDelete'])->name('courses.confirmDelete');
	Route::post('/delete/{course}', [CourseController::class, 'delete'])->name('courses.delete');
	Route::get('/delete/{course}', [CourseController::class, 'delete'])->name('courses.deleteGet');

	// undelete
	Route::get('/deleted', [CourseController::class, 'deleted'])->name('courses.deleted');
	Route::get('/undelete/{id}', [CourseController::class, 'undelete'])->name('courses.undelete');
});

// Lessons
Route::group(['prefix' => 'lessons'], function () {
	Route::get('/', [LessonController::class, 'index'])->name('lessons');

	Route::get('/admin', [LessonController::class, 'admin'])->name('lessons.admin');
	Route::get('/view/{lesson}',[LessonController::class, 'view'])->name('lessons.view');
	Route::post('/view/{lesson}',[LessonController::class, 'view']); // just in case they hit enter on the ajax form
	Route::get('/review-orig/{lesson}/{reviewType?}',[LessonController::class, 'reviewOrig']);
	Route::get('/reviewmc/{lesson}/{reviewType?}',[LessonController::class, 'reviewmc'])->name('lessons.reviewmc');
	Route::get('/review/{lesson}/{reviewType?}',[LessonController::class, 'review'])->name('lessons.review');
	Route::get('/read/{lesson}',[LessonController::class, 'read']);
	Route::get('/log-quiz/{lessonId}/{score}', [LessonController::class, 'logQuiz'])->name('lessons.logQuiz');
	Route::get('/start/{lesson}/', [LessonController::class, 'start'])->name('lessons.start');
	Route::get('/rss/{lesson}/', [LessonController::class, 'rss']);
	Route::get('/rss-reader/{lesson}', [LessonController::class, 'rssReader'])->name('lessons.rssReader');

	// add/create
	Route::get('/add/{course?}',[LessonController::class, 'add'])->name('lessons.add');
	Route::post('/create',[LessonController::class, 'create'])->name('lessons.create');

	// edit/update
	Route::get('/edit/{lesson}',[LessonController::class, 'edit'])->name('lessons.edit');
	Route::post('/update/{lesson}',[LessonController::class, 'update'])->name('lessons.update');
	Route::get('/edit2/{lesson}',[LessonController::class, 'edit2'])->name('lessons.edit2');
	Route::post('/update2/{lesson}',[LessonController::class, 'update2'])->name('lessons.update2');

	// delete
	Route::get('/confirmdelete/{lesson}',[LessonController::class, 'confirmdelete'])->name('lessons.confirmDelete');
	Route::post('/delete/{lesson}',[LessonController::class, 'delete'])->name('lessons.delete');
	Route::get('/undelete/{id}', [LessonController::class, 'undelete'])->name('lessons.undelete');

	// add/create
	Route::get('/publish/{lesson}',[LessonController::class, 'publish'])->name('lessons.publish');
	Route::post('/publishupdate/{lesson}',[LessonController::class, 'publishupdate'])->name('lessons.publishUpdate');

    // convert to list
	Route::get('/convert-to-list/{lesson}',[LessonController::class, 'convertToList'])->name('lessons.convertToList');

	// ajax
	Route::get('/finished/{lesson}',[LessonController::class, 'toggleFinished']);

    // catch all
	Route::get('/{parent_id}', [LessonController::class, 'index']);
});

// Histories
Route::group(['prefix' => 'history'], function () {
	Route::get('/', [HistoryController::class, 'index'])->name('history');
	Route::get('/index', [HistoryController::class, 'index'])->name('history.index');
	Route::get('/admin', [HistoryController::class, 'admin'])->name('history.admin');
    Route::get('/rss', [HistoryController::class, 'rss'])->name('history.rss');

	// view
	Route::get('/show/{history}', [HistoryController::class, 'view'])->name('history.view');

	// add
	Route::get('/add', [HistoryController::class, 'add'])->name('history.add');

	Route::post('/create', [HistoryController::class, 'create'])->name('history.create');

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

// Stats
Route::group(['prefix' => 'stats'], function () {
	Route::get('/', [StatController::class, 'index']);
	Route::get('/admin', [StatController::class, 'admin']);
	Route::get('/index', [StatController::class, 'index']);

	// view
	Route::get('/view/{permalink}', [StatController::class, 'permalink']);
	Route::get('/show/{stat}', [StatController::class, 'view']);

	// add
	Route::get('/add', [StatController::class, 'add']);
	Route::post('/create', [StatController::class, 'create']);

	// edit
	Route::get('/edit/{stat}', [StatController::class, 'edit']);
	Route::post('/update/{stat}', [StatController::class, 'update']);

	// publish
	Route::get('/publish/{stat}', [StatController::class, 'publish']);
	Route::post('/publishupdate/{stat}', [StatController::class, 'updatePublish']);
	Route::get('/publishupdate/{stat}', [StatController::class, 'updatePublish']);

	// delete
	Route::get('/confirmdelete/{stat}', [StatController::class, 'confirmDelete']);
	Route::post('/delete/{stat}', [StatController::class, 'delete']);
	Route::get('/delete/{stat}', [StatController::class, 'delete']);

	// undelete
	Route::get('/deleted', [StatController::class, 'deleted']);
	Route::get('/undelete/{id}', [StatController::class, 'undelete']);
});

// Exercises
Route::group(['prefix' => 'exercises'], function () {
	Route::get('/', [ExerciseController::class, 'index'])->name('exercises');
	Route::get('/admin', [ExerciseController::class, 'admin'])->name('exercises.admin');
	Route::get('/index', [ExerciseController::class, 'index'])->name('exercises.index');

	// view
	Route::get('/view/{permalink}', [ExerciseController::class, 'permalink'])->name('exercises.view');
	Route::get('/show/{exercise}', [ExerciseController::class, 'view'])->name('exercises.show');

	// add - for admin to add exercises to be chosen by users
	Route::get('/add', [ExerciseController::class, 'add'])->name('exercises.add');
	Route::post('/create', [ExerciseController::class, 'create'])->name('exercises.create');

	// edit - for admin to update individual exercises
	Route::get('/edit/{exercise}', [ExerciseController::class, 'edit'])->name('exercises.edit');
	Route::post('/update/{exercise}', [ExerciseController::class, 'update'])->name('exercises.update');

	// for user's to choose, set, and adjust their list
	Route::get('/choose', [ExerciseController::class, 'choose'])->name('exercises.choose');
	Route::post('/set', [ExerciseController::class, 'set'])->name('exercises.set');

	// delete
	Route::get('/confirmdelete/{exercise}', [ExerciseController::class, 'confirmDelete'])->name('exercises.confirmDelete');
	Route::post('/delete/{exercise}', [ExerciseController::class, 'delete'])->name('exercises.delete');
	Route::get('/delete/{exercise}', [ExerciseController::class, 'delete'])->name('exercises.deleteGet');

	// undelete
	Route::get('/deleted', [ExerciseController::class, 'deleted'])->name('exercises.deleted');
	Route::get('/undelete/{id}', [ExerciseController::class, 'undelete'])->name('exercises.undelete');
});

//todo:locale
}); // End of Locale Prefix Handler

// Contacts
Route::group(['prefix' => 'contacts'], function () {
	Route::get('/', [ContactController::class, 'index']);
	Route::get('/index', [ContactController::class, 'index']);

	// view
	Route::get('/view/{contact}', [ContactController::class, 'view']);
	Route::get('/show/{contact}', [ContactController::class, 'view']);

	// add
	Route::get('/add', [ContactController::class, 'add']);
	Route::post('/create', [ContactController::class, 'create']);

	// edit
	Route::get('/edit/{contact}', [ContactController::class, 'edit']);
	Route::post('/update/{contact}', [ContactController::class, 'update']);

	// publish
	Route::get('/publish/{contact}', [ContactController::class, 'publish']);
	Route::post('/publishupdate/{contact}', [ContactController::class, 'updatePublish']);
	Route::get('/publishupdate/{contact}', [ContactController::class, 'updatePublish']);

	// delete
	Route::get('/confirmdelete/{contact}', [ContactController::class, 'confirmDelete']);
	Route::post('/delete/{contact}', [ContactController::class, 'delete']);
	Route::get('/delete/{contact}', [ContactController::class, 'delete']);

	// undelete
	Route::get('/deleted', [ContactController::class, 'deleted']);
	Route::get('/undelete/{id}', [ContactController::class, 'undelete']);
});
