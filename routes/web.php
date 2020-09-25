<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\UserController;

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

Route::get('/', [HomeController::class, 'frontpage'])->name('frontpage');
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [LoginController::class, 'register'])->name('register');
Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');
Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard')->middleware('auth');

// Users
Route::group(['prefix' => 'users'], function () {
	Route::get('/', [UserController::class, 'index']);
	Route::get('/view', [UserController::class, 'view']);

	// add
	Route::get('/add', [UserController::class, 'add']);
	Route::post('/create', [UserController::class, 'create']);
	
	// edit
	Route::get('/edit', [UserController::class, 'edit']);
	Route::post('/update', [UserController::class, 'update']);

	// delete
	Route::get('/confirmdelete', [UserController::class, 'confirmDelete']);
	Route::post('/delete', [UserController::class, 'delete']);
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
	Route::get('/request', [LoginController::class, 'resetPassword'])->name('password.request');
	Route::post('/update', [LoginController::class, 'updatePassword'])->name('password.update');
});

