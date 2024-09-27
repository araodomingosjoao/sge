<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolRegistrationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/school/register', [SchoolRegistrationController::class, 'register']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user/profile', [AuthController::class, 'profile']);
    Route::apiCrud('user', UserController::class);
    Route::apiCrud('school', SchoolController::class, null, [
        'create' => 'role:admin_school',
        'delete' => 'role:admin_school' 
    ]);
});

