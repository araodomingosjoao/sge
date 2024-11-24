<?php

namespace App\Observers;

use App\Models\School;
use App\Services\AcademicYearService;
use App\Services\SchoolDataService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolObserver
{
    private $academicYearService;
    private $schoolDataService;

    public function __construct(
        AcademicYearService $academicYearService, 
        SchoolDataService $schoolDataService
    )
    {
        $this->academicYearService = $academicYearService;
        $this->schoolDataService = $schoolDataService;
    }

    public function created(School $school): void
    {
        DB::transaction(function () use ($school) {
            Log::info("Iniciando a configuração para a escola: {$school->id} - {$school->name}");

            $typeEducation = $school->typeEducation;

            if (!$typeEducation) {
                Log::warning("Tipo de educação não encontrado para a escola: {$school->id}");
                return;
            }

            switch ($typeEducation->name) {
                case 'Ensino Médio':
                    $this->schoolDataService->associateHighSchoolData($school);
                    break;

                case 'Ensino Fundamental':
                    $this->schoolDataService->associateFundamentalData($school);
                    break;

                default:
                    Log::warning("Tipo de educação não suportado: {$typeEducation->name}");
                    break;
            }

            $this->academicYearService->createAcademicYear($school);

            Log::info("Configuração concluída para a escola: {$school->id}");
        });
    }
}
