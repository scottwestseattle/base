<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;

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

Route::group(['prefix' => 'home'], function () {
	
	// index
	Route::get('/view/{tag}', 'HomeController@view');

	// add/create
	Route::get('/add','HomeController@add');
	Route::post('/create','HomeController@create');
	Route::get('/add-user-favorite-list','HomeController@addUserFavoriteList');
	Route::post('/create-user-favorite-list','HomeController@createUserFavoriteList');

	// edit/update
	Route::get('/edit/{tag}','HomeController@edit');
	Route::post('/update/{tag}','HomeController@update');
	Route::get('/edit-user-favorite-list/{tag}','HomeController@editUserFavoriteList');

	// delete / confirm delete
	Route::get('/confirmdelete/{tag}','HomeController@confirmdelete');
	Route::get('/confirm-user-favorite-list-delete/{tag}','HomeController@confirmUserFavoriteListDelete');
	Route::post('/delete/{tag}','HomeController@delete');
	
});
