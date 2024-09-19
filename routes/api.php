<?php

use App\Http\Controllers\SchoolController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiCrud('user', UserController::class);
Route::apiCrud('school', SchoolController::class);
