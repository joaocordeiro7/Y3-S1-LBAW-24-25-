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



Route::controller(PostController::class)->group(function (){
    Route::get('/post/{id}','show');
    Route::get('/home', 'list');

});


Route::controller(PostController::class)->group(function (){
    Route::get('/createPosts','create')->name('createPosts');
    Route::post('/api/createPosts','store')->name('publish');
    Route::post('/post/edit/{id}','update');
    Route::get('/', 'index')->name('home');
    Route::post('/deletePost/{id}','destroy');
    Route::get('/user/{id}/posts', 'showUserPosts')->name('user.posts');
    Route::post('/post/like', 'like');
    Route::post('/comments/store', 'storeComment')->name('comments.store');
    Route::put('/comments/update/{id}', 'updateComment')->name('comments.update');
    Route::post('/comments/reply', 'replyToComment')->middleware('auth');
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
    Route::post('/api/users/{id}/edit', 'edit')->name('updateProfile');
    Route::get('/search', 'search')->name('user.search');
});

// Admin
Route::controller(AdminController:: class)->group(function () {
    Route::get('/admin', 'index')->name('adminDashboard');
    Route::post('/admin/create', 'createUser')->name('createUser');
    Route::post('/api/admin/edit/{id}',  'adminUpdateUser')->name('adminUpdateUser');
});