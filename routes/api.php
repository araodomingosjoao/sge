<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiCrud('user', UserController::class);
Route::apiCrud('post', PostController::class);
Route::apiCrud('comment', CommentController::class);
