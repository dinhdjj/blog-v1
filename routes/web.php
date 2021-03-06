<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect()->route('posts.index');
})->name('home');

/**
 * ===========================================
 * Auth routes
 * ===========================================
 */

Route::get('login', [AuthController::class, 'viewLogin'])
    ->middleware(['guest'])
    ->name('login');
Route::post('login', [AuthController::class, 'login'])
    ->middleware(['guest'])
    ->name('login');
Route::post('logout', [AuthController::class, 'logout'])
    ->middleware(['auth'])
    ->name('logout');

/**
 * ===========================================
 * User routes
 * ===========================================
 */

Route::get('register', [UserController::class, 'viewRegister'])
    ->middleware(['guest'])
    ->name('register');
Route::post('register', [UserController::class, 'register'])
    ->middleware(['guest'])
    ->name('register');

Route::get('profile', [AuthController::class, 'viewUpdateProfile'])
    ->middleware(['auth'])
    ->name('profile.viewUpdate');
Route::patch('profile', [AuthController::class, 'updateProfile'])
    ->middleware(['auth'])
    ->name('profile.update');

Route::prefix('password')->group(function () {
    Route::get('reset', [AuthController::class, 'view'])
        ->middleware(['guest'])
        ->name('password.reset');
    Route::patch('reset', [AuthController::class, 'view'])
        ->middleware(['guest'])
        ->name('password.reset');
    Route::get('change', [AuthController::class, 'viewChangePassword'])
        ->middleware(['auth'])
        ->name('password.viewChange');
    Route::patch('change', [AuthController::class, 'changePassword'])
        ->middleware(['auth'])
        ->name('password.change');
});

/**
 * ===========================================
 * Tag routes
 * ===========================================
 */


/**
 * ===========================================
 * Post routes
 * ===========================================
 */

Route::prefix('posts')->group(function () {
    Route::get('', [PostController::class, 'index'])
        ->name('posts.index');

    Route::get('create', [PostController::class, 'viewCreate'])
        ->middleware(['auth', 'verified', 'can:create,App\Models\Post'])
        ->name('posts.viewCreate');
    Route::post('create', [PostController::class, 'create'])
        ->middleware(['auth', 'verified', 'can:create,App\Models\Post'])
        ->name('posts.create');

    Route::prefix('own')->group(function () {
        Route::get('', [PostController::class, 'viewOwn'])
            ->middleware(['auth', 'verified', 'can:create,App\Models\Post'])
            ->name('posts.viewOwn');
        Route::get('dashboard', [PostController::class, 'viewOwnDashboard'])
            ->middleware(['auth', 'verified', 'can:create,App\Models\Post'])
            ->name('posts.viewOwnDashboard');
    });

    Route::prefix('{post}')->group(function () {
        Route::get('', [PostController::class, 'show'])
            ->name('posts.show');

        Route::get('update', [PostController::class, 'viewUpdate'])
            ->middleware(['auth', 'verified', 'can:update,post'])
            ->name('posts.viewUpdate');
        Route::put('update', [PostController::class, 'update'])
            ->middleware(['auth', 'verified', 'can:update,post'])
            ->name('posts.update');
    });
});
