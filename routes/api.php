<?php

use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolRegistrationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/school/register', [SchoolRegistrationController::class, 'register']);
Route::apiCrud('user', UserController::class);
Route::apiCrud('school', SchoolController::class);
