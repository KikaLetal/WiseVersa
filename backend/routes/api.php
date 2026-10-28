<?php

use App\Http\Controllers\Api\TranslatorController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DictListController;
use App\Http\Controllers\Api\DictItemsController;
use App\Http\Controllers\Api\BlogController;
use Illuminate\Support\Facades\Route;

// translator

// POST /api/translate
Route::post('/translate', [TranslatorController::class, 'translate']);

// auth

// GET api/auth/ping
Route::get('/auth/ping', [AuthController::class, 'ping']);

// POST api/auth/register
Route::post('/auth/register', [AuthController::class, 'register']);

// POST api/auth/login
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // GET api/auth/me
    Route::get('/auth/me', [AuthController::class, 'me']);
});

// dictionary lists

Route::middleware('auth:sanctum')->group(function () {
    // GET /api/dictionary/lists
    Route::get('/dictionary/lists', [DictListController::class, 'getLists']);

    // POST /api/dictionary/lists
    Route::post('/dictionary/lists', [DictListController::class, 'createList']);

    // POST /api/dictionary/lists/{listId}/icon
    Route::post('/dictionary/lists/{listId}/icon', [DictListController::class, 'updateIcon']);
});

// dictionary items

Route::middleware('auth:sanctum')->group(function () {
    // GET /api/dictionary/lists/{listId}/items
    Route::get(
        '/dictionary/lists/{listId}/items',
        [DictItemsController::class, 'getItems']
    );

    // POST /api/dictionary/items
    Route::post(
        '/dictionary/items',
        [DictItemsController::class, 'addItem']
    );

    // DELETE /api/dictionary/lists/{listId}/items/{itemId}
    Route::delete(
        '/dictionary/lists/{listId}/items/{itemId}',
        [DictItemsController::class, 'deleteItem']
    );

    // PATCH /api/dictionary/items/{itemId}/mastered
    Route::patch(
        '/dictionary/items/{itemId}/mastered',
        [DictItemsController::class, 'updateMastered']
    );

    // POST /api/dictionary/lists/{listId}/reset-progress
    Route::post(
        '/dictionary/lists/{listId}/reset-progress',
        [DictItemsController::class, 'resetProgress']
    );
});

// blog

Route::middleware('auth:sanctum')->group(function () {
    // GET /api/blog/posts
    Route::get('/blog/posts', [BlogController::class, 'getPosts']);

    // GET /api/blog/posts/{postId}
    Route::get('/blog/posts/{postId}', [BlogController::class, 'getPost']);

    // POST /api/blog/posts
    Route::post('/blog/posts', [BlogController::class, 'createPost']);

    // PATCH /api/blog/posts/{postId}
    Route::patch('/blog/posts/{postId}', [BlogController::class, 'updatePost']);

    // DELETE /api/blog/posts/{postId}
    Route::delete('/blog/posts/{postId}', [BlogController::class, 'deletePost']);

    // POST /api/blog/posts/{postId}/like
    Route::post('/blog/posts/{postId}/like', [BlogController::class, 'toggleLike']);
});