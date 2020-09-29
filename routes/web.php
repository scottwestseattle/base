<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;

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

// Front Page
Route::get('/', [HomeController::class, 'frontpage'])->name('frontpage');
Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard')->middleware('auth');
Route::get('/about', function() { return view('home.about'); });

// Auth
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');

// Email
Route::group(['prefix' => 'email'], function () {
	Route::get('/send', [EmailController::class, 'send']);
});

// Samples
Route::group(['prefix' => 'samples'], function () {
	Route::get('/', [SampleController::class, 'index']);
	Route::get('/index', [SampleController::class, 'index']);
	Route::get('/view/{sample}', [SampleController::class, 'view']);

	// add
	Route::get('/add', [SampleController::class, 'add']);
	Route::post('/create', [SampleController::class, 'create']);
	
	// edit
	Route::get('/edit/{sample}', [SampleController::class, 'edit']);
	Route::post('/update/{sample}', [SampleController::class, 'update']);

	// delete
	Route::get('/confirmdelete/{sample}', [SampleController::class, 'confirmDelete']);
	Route::post('/delete/{sample}', [SampleController::class, 'delete']);
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
Route::group(['prefix' => 'passwords'], function () {
	Route::get('/edit/{user}', [LoginController::class, 'editPassword']);
	Route::get('/reset/{user}', [LoginController::class, 'editPassword']);
	Route::post('/update/{user}', [LoginController::class, 'updatePassword']);
});

// Password
Route::group(['prefix' => 'home'], function () {
	Route::get('/events/{filter?}', [HomeController::class, 'events'])->middleware('admin');
});

