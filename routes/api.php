<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AllocationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolRegistrationController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/school/register', [SchoolRegistrationController::class, 'register']);
Route::get('/options', [SchoolController::class, 'options']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/setup/step/{step}', [SetupController::class, 'completeStep']);
    Route::get('/setup/status', [SetupController::class, 'checkSetupStatus']);
    Route::apiCrud('schools', SchoolController::class, null, [
        'create' => 'role:admin_school',
        'delete' => 'role:admin_school' 
    ]);
    Route::get('/user/profile', [AuthController::class, 'profile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::apiCrud('user', UserController::class);
    Route::apiCrud('level', LevelController::class);
    Route::apiCrud('teacher', TeacherController::class);
    Route::get('/school/{id}', [SchoolController::class, 'read']);
    Route::post('/school/{id}', [SchoolController::class, 'update']);


    Route::get('/disciplines', [DisciplineController::class, 'index']);
    Route::get('/disciplines/levels/{level}', [DisciplineController::class, 'getLevelDisciplines']);
    Route::post('/disciplines/levels/{level}', [DisciplineController::class, 'addLevelDiscipline']);
    Route::delete('/disciplines/{discipline}/levels/{level}/', [DisciplineController::class, 'removeLevelDiscipline']);

    Route::get('/academic-years', [AcademicYearController::class, 'showAcademicYearWithTrimesters']);
    Route::put('/academic-years/{academicYear}/trimesters', [AcademicYearController::class, 'updateTrimesters']);
    Route::put('/academic-years/{academicYear}', [AcademicYearController::class, 'updateAcademicYear']);

    Route::post('/disciplines/courses/{course}', [SchoolController::class, 'addCourseDiscipline']);
    Route::delete('/disciplines/{discipline}/courses/{course}/disciplines', [SchoolController::class, 'removeCourseDiscipline']);
    
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
    Route::apiCrud('students', StudentController::class);
    Route::post('/students/enrollment', [StudentController::class, 'enroll']);
    Route::put('/students/{student_id}/enrollment/{enrollment_id}', [StudentController::class, 'enroll']);
    Route::get('/students/{student_id}/enrollments', [StudentController::class, 'getStudentEnrollments']);
    Route::post('/students/{student_id}/transfer', [StudentController::class, 'transferStudent']);
    Route::delete('/students/{student_id}/enrollment/{enrollment_id}', [StudentController::class, 'cancelEnrollment']);
    Route::get('/students/class/{class_id}', [StudentController::class, 'getClassStudents']);

});
