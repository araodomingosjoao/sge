<?php

namespace App\Services;

use App\Enums\TypeEducationEnum;
use App\Models\Course;
use App\Models\Discipline;
use App\Models\School;
use App\Models\SchoolCourse;
use App\Models\SchoolCourseDiscipline;
use App\Models\SchoolLevel;
use App\Models\SchoolLevelDiscipline;
use App\Models\Level;
use App\Models\LevelCourseDiscipline;
use App\Models\SchoolDiscipline;
use Illuminate\Support\Facades\Log;

class SchoolStructureService
{
    public function setupSchoolStructure(School $school): void
    {
        match ($school->typeEducation->name) {
            'Ensino Médio' => $this->setupHighSchool($school),
            'Ensino Fundamental' => $this->setupFundamental($school),
            default => Log::warning("Tipo de educação não suportado: {$school->typeEducation->name}")
        };
    }

    private function setupHighSchool(School $school): void
    {
        \Log::info("Configurando Ensino Médio para escola:", [
            'school_id' => $school->id,
            'category_id' => $school->category_id
        ]);

        // 1. Primeiro criar SchoolDisciplines para todas as disciplinas dos cursos da categoria
        $courseDisciplines = Discipline::whereHas('courses', function ($query) use ($school) {
            $query->where('category_id', $school->category_id);
        })->get();

        \Log::info("Disciplinas encontradas nos cursos:", [
            'count' => $courseDisciplines->count(),
            'disciplines' => $courseDisciplines->pluck('name')
        ]);

        // Criar SchoolDiscipline para cada disciplina
        foreach ($courseDisciplines as $discipline) {
            SchoolDiscipline::firstOrCreate([
                'school_id' => $school->id,
                'discipline_id' => $discipline->id
            ]);
        }

        // 2. Criar associações com cursos
        $courses = Course::where('category_id', $school->category_id)->get();
        foreach ($courses as $course) {
            \Log::info("Processando curso:", [
                'course_id' => $course->id,
                'course_name' => $course->name
            ]);

            foreach ($course->disciplines as $discipline) {
                SchoolCourseDiscipline::firstOrCreate([
                    'school_id' => $school->id,
                    'course_id' => $course->id,
                    'discipline_id' => $discipline->id
                ]);
            }
        }

        // 3. Criar associações com níveis
        $levels = Level::where('type_education_id', TypeEducationEnum::HIGH_SCHOOL)->get();
        
        foreach ($levels as $level) {
            \Log::info("Processando nível:", [
                'level_id' => $level->id,
                'level_year' => $level->year
            ]);

            // Criar SchoolLevel
            SchoolLevel::firstOrCreate([
                'school_id' => $school->id,
                'level_id' => $level->id
            ]);

            // Buscar disciplinas do nível relacionadas aos cursos da categoria
            $levelCourseDisciplines = LevelCourseDiscipline::where('level_id', $level->id)
                ->whereHas('course', function ($query) use ($school) {
                    $query->where('category_id', $school->category_id);
                })
                ->get();

            \Log::info("Disciplinas encontradas para nível:", [
                'count' => $levelCourseDisciplines->count(),
                'level_year' => $level->year,
                'disciplines' => $levelCourseDisciplines->map(fn($lcd) => [
                    'discipline_id' => $lcd->discipline_id,
                    'course_id' => $lcd->course_id
                ])
            ]);

            foreach ($levelCourseDisciplines as $lcd) {
                SchoolLevelDiscipline::firstOrCreate([
                    'school_id' => $school->id,
                    'level_id' => $level->id,
                    'discipline_id' => $lcd->discipline_id,
                    'course_id' => $lcd->course_id
                ]);
            }
        }
    }

    private function setupFundamental(School $school): void
    {
        Log::info("Iniciando configuração do Ensino Fundamental para escola: {$school->id}");

        $typeEducationIds = $school->type_education_id == TypeEducationEnum::FUNDAMENTAL_COMPLETE
            ? [TypeEducationEnum::FUNDAMENTAL_INITIAL, TypeEducationEnum::FUNDAMENTAL_FINAL]
            : [$school->type_education_id];

        $levels = Level::whereIn('type_education_id', $typeEducationIds)
            ->with('disciplines')
            ->get();

        foreach ($levels as $level) {
            Log::info("Processando nível: {$level->year}º ano");

            SchoolLevel::create([
                'school_id' => $school->id,
                'level_id' => $level->id
            ]);

            foreach ($level->disciplines as $discipline) {
                SchoolDiscipline::firstOrCreate([
                    'school_id' => $school->id,
                    'discipline_id' => $discipline->id
                ]);

                SchoolLevelDiscipline::create([
                    'school_id' => $school->id,
                    'level_id' => $level->id,
                    'discipline_id' => $discipline->id
                ]);
            }
        }
    }

    private function associateCoursesToSchool(School $school): void
    {
        Log::info("Associando cursos à escola: {$school->id}");

        $courses = Course::where('category_id', $school->category_id)->get();

        foreach ($courses as $course) {
            SchoolCourse::create([
                'school_id' => $school->id,
                'course_id' => $course->id
            ]);
        }
    }
}
