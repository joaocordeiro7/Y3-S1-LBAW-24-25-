<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;


use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\PostController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home
Route::redirect('/', '/login');

// Cards
Route::controller(CardController::class)->group(function () {
    Route::get('/cards', 'list')->name('cards');
    Route::get('/cards/{id}', 'show');
});


// API
Route::controller(CardController::class)->group(function () {
    Route::put('/api/cards', 'create');
    Route::delete('/api/cards/{card_id}', 'delete');
});



Route::controller(PostController::class)->group(function (){
    Route::get('/post/{id}','show');
    Route::get('/home', 'list');

});


Route::controller(PostController::class)->group(function (){
    Route::get('/createPosts','create')->name('createPosts');
    Route::post('/api/createPosts','store')->name('publish');
    Route::post('/post/edit/{id}','update');
    Route::get('/', 'index')->name('home');
    Route::get('/posts', 'index')->name('posts.index');
    Route::post('/deletePost/{id}','destroy');
});


Route::controller(ItemController::class)->group(function () {
    Route::put('/api/cards/{card_id}', 'create');
    Route::post('/api/item/{id}', 'update');
    Route::delete('/api/item/{id}', 'delete');
});


// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

Route::controller(UserController::class)->group(function () {
    Route::get('/users/{id}', 'show')->name('profile');
    Route::get('/users/{id}/edit', 'editUser')->name('editProfile');
    Route::post('/users/{id}/edit', 'edit')->name('updateProfile');
});

// Admin
Route::controller(AdminController:: class)->group(function () {
    Route::get('/admin', 'index')->name('adminDashboard');
    Route::post('/admin', 'createUser')->name('createUser');
    Route::post('/admin/edit/{id}',  'adminUpdateUser')->name('adminUpdateUser');
});