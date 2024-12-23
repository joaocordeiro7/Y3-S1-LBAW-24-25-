<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\CardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\StaticPageController;


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
Route::redirect('/', '/home');


Route::view('/features', 'static.features')->name('features');

Route::view('/aboutUs', 'static.aboutUs')->name('aboutUs');

Route::view('/contacts', 'static.contacts')->name('contacts');

Route::post('/contacts/feedback', [StaticPageController::class, 'submitFeedback'])->name('feedback.submit');



Route::controller(PostController::class)->group(function (){
    Route::get('/post/{id}','show')->name('post');
    Route::get('/home', 'list');

});


Route::controller(PostController::class)->group(function (){
    Route::get('/createPosts','create')->middleware('checkIfBlocked')->name('createPosts');
    Route::post('/api/createPosts','store')->name('publish');
    Route::post('/post/edit/{id}','update');
    Route::get('/', 'index')->name('home');
    Route::post('/deletePost/{id}','destroy');
    Route::get('/user/{id}/posts', 'showUserPosts')->name('user.posts');
    Route::post('/api/getMorePosts','getMorePosts');
    Route::post('/post/like', 'like');
    Route::post('/comment/vote', 'voteComment')->name('comment.vote');
    Route::post('/comments/store', 'storeComment')->name('comments.store');
    Route::put('/comments/update/{id}', 'updateComment')->name('comments.update');
    Route::post('/comments/reply', 'replyToComment')->middleware('auth');
    Route::put('/comments/reply/update/{id}', 'updateComment');
    Route::delete('/comments/delete/{id}', 'deleteComment')->middleware('auth');
    Route::get('/posts/tag/{tag}', 'filterByTag')->name('posts.tag');
    Route::post('/api/getMoreTagPosts','getMoreTagPosts');

});



// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register')->middleware('blacklist');
});

Route::controller(UserController::class)->group(function () {
    Route::get('/users/{id}', 'show')->name('profile');
    Route::get('/users/{id}/edit', 'editUser')->name('editProfile');
    Route::post('/api/users/{id}/edit', 'edit')->name('updateProfile');
    Route::get('/search', 'search')->name('user.search');
    Route::post('/api/follow/{userToFollow}','follow');
    Route::post('/api/unfollow/{userToUnfollow}','unfollow');
    Route::post('/api/checkNotf','getNewNotf')->name('checkForNewNotfs');
    Route::post('/api/readNotf','readNotf');
    Route::delete('/users/delete/{id}', 'deleteAccount')->name('deleteAccount');  
    Route::post('/user/propose-topic', 'proposeTopic')->middleware('auth')->name('proposeTopic');
    Route::get('/users/{id}/followers', 'followers')->name('user.followers');
    Route::get('/users/{id}/following', 'following')->name('user.following');
    Route::get('/users/{id}/followedTags','followedTags');
    Route::post('/followTag','followTag');
    Route::post('/unfollowTag','unfollowTag');
});

// Admin
Route::controller(AdminController:: class)->group(function () {
    Route::get('/admin', 'index')->name('adminDashboard');
    Route::post('/admin/create', 'createUser')->name('createUser');
    Route::post('/api/admin/edit/{id}',  'adminUpdateUser')->name('adminUpdateUser');
    Route::delete('/admin/delete/{id}', 'adminDeleteAccount')->name('adminDeleteAccount');
    Route::post('/admin/block/{id}', 'blockUser')->name('blockUser');
    Route::delete('/admin/unblock/{id}', 'unblockUser')->name('unblockUser');
    Route::post('/admin/proposals/{id}/accept', 'acceptTopicProposal')->name('acceptTopicProposal');
    Route::delete('/admin/proposals/{id}/discard', 'discardTopicProposal')->name('discardTopicProposal');
    Route::post('/admin/promote/{id}', 'promoteToAdmin')->name('promoteToAdmin');
});

Route::controller(ImageController:: class)->group(function () {
    Route::post('/image/update', 'update')->name('image.update');
    Route::delete('/image/delete/{id}', 'delete')->name('image.delete');
});

Route::controller(PasswordResetController:: class)->group(function () {
    Route::get('password/reset', 'showResetRequestForm')->name('password.request');
    Route::post('password/reset', 'sendResetLink')->name('password.email');
    Route::post('api/password/reset/{token}', 'resetPassword')->name('password.update');
    Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
});
