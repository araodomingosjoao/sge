<?php

namespace App\Observers;

use App\Models\Level;
use App\Models\School;
use App\Models\SchoolCourse;
use App\Models\SchoolCourseDiscipline;
use App\Models\SchoolLevel;
use App\Models\SchoolLevelDiscipline;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

class SchoolObserver
{
    public function created(School $school)
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
                    $this->associateHighSchoolData($school);
                    break;

                case 'Ensino Fundamental':
                    $this->associateFundamentalData($school);
                    break;

                default:
                    break;
            }

            Log::info("Configuração concluída para a escola: {$school->id}");
        });
    }

    private function associateHighSchoolData(School $school)
    {
        Log::info("Associando dados de Ensino Médio para a escola: {$school->id}");

        $this->associateCoursesAndDisciplines($school);
        $this->associateLevelsAndDisciplines($school);

        Log::info("Dados de Ensino Médio associados para a escola: {$school->id}");
    }

    private function associateCoursesAndDisciplines(School $school)
    {
        Log::info("Associando cursos e disciplinas para a escola: {$school->id}");

        if ($school->category) {
            Log::info("Processando categoria: {$school->category->id} - {$school->category->name}");

            foreach ($school->category->courses as $course) {
                SchoolCourse::firstOrCreate([
                    'school_id' => $school->id,
                    'course_id' => $course->id,
                ]);

                foreach ($course->disciplines as $discipline) {
                    SchoolCourseDiscipline::firstOrCreate([
                        'school_id' => $school->id,
                        'course_id' => $course->id,
                        'discipline_id' => $discipline->id,
                    ]);
                }
            }
        }
    }


    private function associateLevelsAndDisciplines(School $school)
    {
        Log::info("Associando níveis e disciplinas para a escola: {$school->id}");

        // If school has no category or courses, exit early
        if (!$school->category || $school->category->courses->isEmpty()) {
            Log::warning("Nenhuma categoria ou curso encontrado para a escola: {$school->id}");
            return;
        }

        // Get all disciplines from the school's courses
        $courseDisciplines = $school->category->courses->flatMap(function ($course) {
            return $course->disciplines;
        })->unique('id');

        foreach ($school->typeEducation->levels as $level) {
            Log::info("Processando nível: {$level->id} - Ano: {$level->year}");

            // Filter disciplines that are both in the level and in the course disciplines
            $validDisciplines = $level->disciplines->intersect($courseDisciplines);

            foreach ($validDisciplines as $discipline) {
                if (!SchoolLevelDiscipline::where('school_id', $school->id)
                    ->where('level_id', $level->id)
                    ->where('discipline_id', $discipline->id)->exists()) {

                    SchoolLevelDiscipline::create([
                        'school_id' => $school->id,
                        'level_id' => $level->id,
                        'discipline_id' => $discipline->id,
                    ]);
                    Log::info("Disciplina associada ao nível: {$discipline->id} - {$discipline->name}");
                }
            }
        }
    }


    private function associateFundamentalData(School $school)
    {
        Log::info("Associando dados de Ensino Fundamental para a escola: {$school->id}");

        $typeEducationIds = $school->type_education_id == 3
            ? [1, 2]
            : [$school->type_education_id];

        $levels = Level::whereIn('type_education_id', $typeEducationIds)->get();


        foreach ($levels as $level) {
            Log::info("Processando nível: {$level->id} - Ano: {$level->year}");

            // Associa o nível à escola
            SchoolLevel::firstOrCreate([
                'school_id' => $school->id,
                'level_id' => $level->id,
            ]);

            // Associa apenas disciplinas relacionadas a este nível
            foreach ($level->disciplines as $discipline) {
                if (!SchoolLevelDiscipline::where('school_id', $school->id)
                    ->where('level_id', $level->id)
                    ->where('discipline_id', $discipline->id)->exists()) {

                    SchoolLevelDiscipline::create([
                        'school_id' => $school->id,
                        'level_id' => $level->id,
                        'discipline_id' => $discipline->id,
                    ]);
                    Log::info("Disciplina associada ao nível: {$discipline->id} - {$discipline->name}");
                }
            }
        }
    }
}
