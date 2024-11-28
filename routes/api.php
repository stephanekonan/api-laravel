<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\CommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {

    // ROUTES AUTH
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    // Routes publiques - Pas besoin d'être connecté
    Route::get('posts', [PostController::class, 'index']);
    Route::get('posts/{post}', [PostController::class, 'show']);
    Route::get('posts/feeded', [PostController::class, 'feeded']);

    Route::get('events', [EventController::class, 'index']);
    Route::get('events/{event}', [EventController::class, 'show']);

    Route::get('comments', [CommentController::class, 'index']);
    Route::get('comments/{comment}', [CommentController::class, 'show']);

    Route::get('likes/count/{likeable_id}/{likeable_type}', [LikeController::class, 'count']);


    // Routes protégées - Authentification requise
    Route::middleware(['auth.middleware'])->group(function () {

        Route::delete('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/profil', [AuthController::class, 'profil']);

        Route::post('posts', [PostController::class, 'store'])
        ->middleware('can:create,App\Models\Post');

        Route::put('posts/{post}', [PostController::class, 'update'])
        ->middleware('can:update,post');

        Route::delete('posts/{post}', [PostController::class, 'destroy'])
        ->middleware('can:delete,post');

        Route::post('events', [EventController::class, 'store'])
        ->middleware('can:create,App\Models\Event');

        Route::put('events/{event}', [EventController::class, 'update'])
        ->middleware('can:update,event');

        Route::delete('events/{event}', [EventController::class, 'destroy'])
        ->middleware('can:delete,event');

        Route::post('comments', [CommentController::class, 'store'])
        ->middleware('can:create,App\Models\Comment');

        Route::put('comments/{comment}', [CommentController::class, 'update'])
        ->middleware('can:update,comment');

        Route::delete('comments/{comment}', [CommentController::class, 'destroy'])
        ->middleware('can:delete,comment');

        Route::post('likes', [LikeController::class, 'like']);
    });
});
