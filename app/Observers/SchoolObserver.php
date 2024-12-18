<?php

namespace App\Observers;

use App\Models\School;
use App\Models\SchoolCourseDiscipline;
use App\Models\SchoolLevelDiscipline;
use App\Services\AcademicYearService;
use App\Services\SchoolStructureService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolObserver
{
    public function created(School $school): void
    {
        DB::transaction(function () use ($school) {
            \Log::info("Observer - Criando estrutura para escola:", [
                'school_id' => $school->id,
                'type_education' => $school->typeEducation->name,
                'category_id' => $school->category_id
            ]);

            $schoolStructureService = new SchoolStructureService();
            $schoolStructureService->setupSchoolStructure($school);

            // Verificar se as associações foram criadas
            $levelDisciplinesCount = SchoolLevelDiscipline::where('school_id', $school->id)->count();
            $courseDisciplinesCount = SchoolCourseDiscipline::where('school_id', $school->id)->count();

            \Log::info("Observer - Contagem de associações criadas:", [
                'school_level_disciplines' => $levelDisciplinesCount,
                'school_course_disciplines' => $courseDisciplinesCount
            ]);
            
            (new AcademicYearService())->createAcademicYear($school);
        });
    }
}