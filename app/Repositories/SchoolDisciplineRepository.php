<?php

namespace App\Repositories;

use App\Enums\TypeEducationEnum;
use App\Models\Course;
use App\Models\Level;
use App\Models\SchoolDiscipline;
use App\Models\School;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class SchoolDisciplineRepository extends BaseRepository
{
    protected $relationships = ['discipline', 'school', 'coordinator'];

    protected $searchable = [
        'disciplines.name',
        'disciplines.description'
    ];

    protected $filterable = [
        'is_active',
        'workload',
        'coordinator_id'
    ];

    public function __construct(SchoolDiscipline $model)
    {
        parent::__construct($model);
    }

    /**
     * Busca disciplinas por nome ou descrição
     */
    public function search(string $query): Collection
    {
        return $this->model
            ->whereHas('discipline', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->where('school_id', Auth::user()->school_id)
            ->with('discipline')
            ->get();
    }

    /**
     * Retorna disciplinas associadas a um nível específico na escola
     */
    public function getByLevel(string $levelId): Collection
    {
        return $this->model
            ->where('school_id', Auth::user()->school_id)
            ->whereHas('schoolLevelDisciplines', function ($query) use ($levelId) {
                $query->where('level_id', $levelId);
            })
            ->with(['discipline', 'schoolLevelDisciplines' => function ($query) use ($levelId) {
                $query->where('level_id', $levelId)
                    ->with('course');
            }])
            ->get();
    }

    /**
     * Retorna disciplinas associadas a um curso específico na escola
     */
    public function getByCourse(string $courseId): Collection
    {
        return $this->model
            ->where('school_id', Auth::user()->school_id)
            ->whereHas('schoolCourseDisciplines', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->with(['discipline', 'schoolCourseDisciplines' => function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            }])
            ->get();
    }

    /**
     * Retorna a estrutura completa de disciplinas para Ensino Fundamental
     */
    

    /**
     * Retorna a estrutura completa de disciplinas para Ensino Médio
     */
    public function getHighSchoolStructure(School $school): array
    {
        \Log::info('Buscando estrutura do Ensino Médio para escola:', [
            'school_id' => $school->id
        ]);

        // 1. Buscar todos os níveis da escola
        $levels = Level::where('type_education_id', TypeEducationEnum::HIGH_SCHOOL)
            ->with(['schoolLevelDisciplines' => function ($query) use ($school) {
                $query->where('school_id', $school->id)
                    ->with(['discipline', 'course']);
            }])
            ->get()
            ->map(function ($level) {
                return [
                    'id' => $level->id,
                    'name' => $level->name,
                    'year' => $level->year,
                    'disciplines' => $level->schoolLevelDisciplines->map(function ($sld) {
                        return [
                            'id' => $sld->discipline->id,
                            'name' => $sld->discipline->name,
                            'course' => $sld->course ? [
                                'id' => $sld->course->id,
                                'name' => $sld->course->name
                            ] : null
                        ];
                    })
                ];
            });

        // 2. Buscar todos os cursos da escola
        $courses = Course::where('category_id', $school->category_id)
            ->with(['schoolCourseDisciplines' => function ($query) use ($school) {
                $query->where('school_id', $school->id)
                    ->with('discipline');
            }])
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->name,
                    'disciplines' => $course->schoolCourseDisciplines->map(function ($scd) {
                        return [
                            'id' => $scd->discipline->id,
                            'name' => $scd->discipline->name
                        ];
                    })
                ];
            });

        \Log::info('Estrutura encontrada:', [
            'levels_count' => $levels->count(),
            'courses_count' => $courses->count(),
            'levels' => $levels,
            'courses' => $courses
        ]);

        return [
            'levels' => $levels,
            'courses' => $courses
        ];
    }

    public function getFundamentalStructure(School $school): array
    {
        \Log::info('Buscando estrutura do Ensino Fundamental para escola:', [
            'school_id' => $school->id
        ]);

        $typeEducationIds = $school->type_education_id == 3
            ? [1, 2]
            : [$school->type_education_id];

        \Log::info('Type Education IDs:', $typeEducationIds);

        $disciplines = $this->model
            ->where('school_id', $school->id)
            ->with(['discipline', 'schoolLevelDisciplines' => function ($query) use ($typeEducationIds) {
                $query->whereHas('level', function ($q) use ($typeEducationIds) {
                    $q->whereIn('type_education_id', $typeEducationIds);
                })->with('level');
            }])
            ->get();

        \Log::info('Disciplinas encontradas:', [
            'count' => $disciplines->count(),
            'disciplines' => $disciplines->map(fn($d) => [
                'id' => $d->id,
                'discipline_name' => $d->discipline?->name,
                'level_disciplines_count' => $d->schoolLevelDisciplines->count()
            ])
        ]);

        return $this->formatFundamentalStructure($disciplines);
    }

    /**
     * Associa disciplinas a um nível em lote
     */
    public function associateWithLevel(string $levelId, array $disciplineIds, ?string $courseId = null): void
    {
        $schoolId = Auth::user()->school_id;

        foreach ($disciplineIds as $disciplineId) {
            // Primeiro garante que existe SchoolDiscipline
            $schoolDiscipline = $this->model->firstOrCreate([
                'school_id' => $schoolId,
                'discipline_id' => $disciplineId
            ]);

            // Depois cria a associação com o nível
            $schoolDiscipline->schoolLevelDisciplines()->firstOrCreate([
                'level_id' => $levelId,
                'course_id' => $courseId,
                'school_id' => $schoolId
            ]);
        }
    }

    /**
     * Associa disciplinas a um curso em lote
     */
    public function associateWithCourse(string $courseId, array $disciplineIds, array $levelIds): void
    {
        $schoolId = Auth::user()->school_id;

        foreach ($disciplineIds as $disciplineId) {
            // Primeiro garante que existe SchoolDiscipline
            $schoolDiscipline = $this->model->firstOrCreate([
                'school_id' => $schoolId,
                'discipline_id' => $disciplineId
            ]);

            // Cria associação com o curso
            $schoolDiscipline->schoolCourseDisciplines()->firstOrCreate([
                'course_id' => $courseId,
                'school_id' => $schoolId
            ]);

            // Cria associações com os níveis
            foreach ($levelIds as $levelId) {
                $schoolDiscipline->schoolLevelDisciplines()->firstOrCreate([
                    'level_id' => $levelId,
                    'course_id' => $courseId,
                    'school_id' => $schoolId
                ]);
            }
        }
    }

    private function formatFundamentalStructure(Collection $disciplines): array
    {
        $levels = [];

        foreach ($disciplines as $schoolDiscipline) {
            foreach ($schoolDiscipline->schoolLevelDisciplines as $sld) {
                $levelId = $sld->level_id;
                if (!isset($levels[$levelId])) {
                    $levels[$levelId] = [
                        'id' => $levelId,
                        'name' => $sld->level->name,
                        'year' => $sld->level->year,
                        'disciplines' => []
                    ];
                }

                $levels[$levelId]['disciplines'][] = [
                    'id' => $schoolDiscipline->discipline->id,
                    'name' => $schoolDiscipline->discipline->name
                ];
            }
        }

        return array_values($levels);
    }

    private function formatHighSchoolStructure(Collection $disciplines): array
    {
        $levels = [];
        $courses = [];

        foreach ($disciplines as $schoolDiscipline) {
            // Agrupar por níveis
            foreach ($schoolDiscipline->schoolLevelDisciplines as $sld) {
                $levelId = $sld->level_id;
                if (!isset($levels[$levelId])) {
                    $levels[$levelId] = [
                        'id' => $levelId,
                        'name' => $sld->level->name,
                        'year' => $sld->level->year,
                        'disciplines' => []
                    ];
                }

                $levels[$levelId]['disciplines'][] = [
                    'id' => $schoolDiscipline->discipline->id,
                    'name' => $schoolDiscipline->discipline->name,
                    'course' => $sld->course ? [
                        'id' => $sld->course->id,
                        'name' => $sld->course->name
                    ] : null
                ];
            }

            // Agrupar por cursos
            foreach ($schoolDiscipline->schoolCourseDisciplines as $scd) {
                $courseId = $scd->course_id;
                if (!isset($courses[$courseId])) {
                    $courses[$courseId] = [
                        'id' => $courseId,
                        'name' => $scd->course->name,
                        'disciplines' => []
                    ];
                }

                $courses[$courseId]['disciplines'][] = [
                    'id' => $schoolDiscipline->discipline->id,
                    'name' => $schoolDiscipline->discipline->name
                ];
            }
        }

        return [
            'levels' => array_values($levels),
            'courses' => array_values($courses)
        ];
    }
}
