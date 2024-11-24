<?php

namespace App\Services;

use App\Models\School;
use App\Models\SchoolCourse;
use App\Models\SchoolCourseDiscipline;
use App\Models\SchoolLevel;
use App\Models\SchoolLevelDiscipline;
use App\Models\Level;
use Illuminate\Support\Facades\Log;

class SchoolDataService
{
    /**
     * Configura os dados de Ensino Médio.
     *
     * @param  School  $school
     * @return void
     */
    public function associateHighSchoolData(School $school): void
    {
        Log::info("Associando dados de Ensino Médio para a escola: {$school->id}");

        $this->associateCoursesAndDisciplines($school);
        $this->associateLevelsAndDisciplines($school);

        Log::info("Dados de Ensino Médio associados para a escola: {$school->id}");
    }

    /**
     * Configura os dados de Ensino Fundamental.
     *
     * @param  School  $school
     * @return void
     */
    public function associateFundamentalData(School $school): void
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

            // Associa disciplinas do nível
            foreach ($level->disciplines as $discipline) {
                SchoolLevelDiscipline::firstOrCreate([
                    'school_id' => $school->id,
                    'level_id' => $level->id,
                    'discipline_id' => $discipline->id,
                ]);
                Log::info("Disciplina associada ao nível: {$discipline->id} - {$discipline->name}");
            }
        }
    }

    /**
     * Associa cursos e disciplinas à escola.
     *
     * @param  School  $school
     * @return void
     */
    public function associateCoursesAndDisciplines(School $school): void
    {
        Log::info("Associando cursos e disciplinas para a escola: {$school->id}");

        if ($school->category) {
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

    /**
     * Associa níveis e disciplinas à escola.
     *
     * @param  School  $school
     * @return void
     */
    public function associateLevelsAndDisciplines(School $school): void
    {
        Log::info("Associando níveis e disciplinas para a escola: {$school->id}");

        if (!$school->category || $school->category->courses->isEmpty()) {
            Log::warning("Nenhuma categoria ou curso encontrado para a escola: {$school->id}");
            return;
        }

        $courseDisciplines = $school->category->courses->flatMap(fn($course) => $course->disciplines)->unique('id');

        foreach ($school->typeEducation->levels as $level) {
            Log::info("Processando nível: {$level->id} - Ano: {$level->year}");

            $validDisciplines = $level->disciplines->intersect($courseDisciplines);

            foreach ($validDisciplines as $discipline) {
                SchoolLevelDiscipline::firstOrCreate([
                    'school_id' => $school->id,
                    'level_id' => $level->id,
                    'discipline_id' => $discipline->id,
                ]);
                Log::info("Disciplina associada ao nível: {$discipline->id} - {$discipline->name}");
            }
        }
    }
}
