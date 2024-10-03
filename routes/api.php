<?php

use App\Http\Controllers\AllocationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolRegistrationController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/school/register', [SchoolRegistrationController::class, 'register']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::apiCrud('school', SchoolController::class, null, [
        'create' => 'role:admin_school',
        'delete' => 'role:admin_school' 
    ]);
    Route::get('/user/profile', [AuthController::class, 'profile']);
    Route::apiCrud('user', UserController::class);
    Route::apiCrud('level', LevelController::class);
    Route::apiCrud('discipline', DisciplineController::class);
    Route::apiCrud('teacher', TeacherController::class);
    Route::apiCrud('class', ClassController::class);
    Route::apiCrud('classroom', ClassroomController::class);
    Route::apiCrud('allocations', AllocationController::class);
    Route::get('/allocations/discipline/{discipline_id}/teachers', [AllocationController::class, 'getTeachersByDiscipline']);
    Route::get('/allocations/class/{class_id}/teachers', [AllocationController::class, 'getTeachersByClass']);
    Route::apiCrud('schedule', ScheduleController::class);
    Route::get('/schedule/class/{class_id}', [ScheduleController::class, 'getSchedulesByClass']);
    Route::get('/schedule/teacher/{teacher_id}', [ScheduleController::class, 'getSchedulesByTeacher']);
    Route::get('/schedule/discipline/{discipline_id}', [ScheduleController::class, 'getSchedulesByDiscipline']);
    Route::get('/schedule/teacher/{teacher_id}/availability', [ScheduleController::class, 'checkTeacherAvailability']);
    Route::get('/schedule/day/{day}', [ScheduleController::class, 'getSchedulesByDay']);
    Route::get('/schedule/classroom/availability', [ScheduleController::class, 'checkClassroomAvailability']);
    Route::get('/schedule/class/{class_id}/weekly', [ScheduleController::class, 'getWeeklyScheduleByClass']);

});
