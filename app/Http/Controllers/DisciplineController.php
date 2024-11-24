<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Course;
use App\Models\Discipline;
use App\Models\Level;
use App\Models\SchoolCourseDiscipline;
use App\Models\SchoolLevelDiscipline;
use App\Repositories\DisciplineRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Discipline",
 *     description="API Endpoints for Discipline"
 * )
 */
class DisciplineController extends Controller
{
    protected $repository;

    public function __construct(DisciplineRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *     path="/disciplines",
     *     summary="Lista disciplinas associadas a uma escola",
     *     description="Recupera as disciplinas vinculadas a níveis e cursos de uma escola específica.",
     *     tags={"Discipline"},
     *     security={{ "sanctum": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Dados de disciplinas da escola",
     *         @OA\JsonContent(
     *             @OA\Property(property="school_id", type="string", format="uuid"),
     *             @OA\Property(property="type_education", type="string"),
     *             @OA\Property(property="levels", type="array", @OA\Items()),
     *             @OA\Property(property="courses", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Nenhuma escola associada ao usuário logado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Nenhuma escola associada ao usuário logado.")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $school = Auth::user()->school;

        if (!$school) {
            return ApiResponse::error('Nenhuma escola associada ao usuário logado.', 404);
        }
        // Definir os IDs do tipo de educação com base no tipo de educação da escola
        $typeEducationIds = $school->type_education_id == 3
            ? [1, 2]
            : [$school->type_education_id];

        // Carregar os níveis de educação filtrados pelo tipo de educação
        $levels = Level::whereIn('type_education_id', $typeEducationIds)->get();

        // Para Ensino Fundamental, use disciplinas gerais
        if ($school->typeEducation->name == 'Ensino Fundamental') {
            $mappedLevels = $levels->map(function ($level) {
                return [
                    'id' => $level->id,
                    'name' => $level->name,
                    'year' => $level->year,
                    'disciplines' => $level->disciplines->map(function ($discipline) {
                        return [
                            'id' => $discipline->id,
                            'name' => $discipline->name
                        ];
                    }),
                ];
            });

            return ApiResponse::success([
                'school_id' => $school->id,
                'type_education' => $school->typeEducation->name,
                'levels' => $mappedLevels,
                'courses' => [],
            ]);
        }

        // Para Ensino Médio
        // Verificar se a escola tem categoria e cursos
        if (!$school->category || $school->category->courses->isEmpty()) {
            return ApiResponse::success([
                'school_id' => $school->id,
                'type_education' => $school->typeEducation->name,
                'levels' => [],
                'courses' => [],
            ]);
        }

        // Obter disciplinas dos cursos da escola
        $courseDisciplines = $school->category->courses->flatMap(function ($course) {
            return $course->disciplines;
        })->unique('id');

        // Mapear os níveis filtrados
        $mappedLevels = $levels->map(function ($level) use ($courseDisciplines, $school) {
            // Filtrar disciplinas que estão no nível e nos cursos da escola
            $validDisciplines = $level->disciplines->intersect($courseDisciplines);

            return [
                'id' => $level->id,
                'name' => $level->name,
                'year' => $level->year,
                'disciplines' => $validDisciplines->map(function ($discipline) {
                    return [
                        'id' => $discipline->id,
                        'name' => $discipline->name
                    ];
                }),
            ];
        });

        // Carregar cursos da escola
        $courses = $school->category->courses->map(function ($course) use ($school) {
            return [
                'id' => $course->id,
                'name' => $course->name,
                'disciplines' => $course->disciplines->map(function ($discipline) {
                    return [
                        'id' => $discipline->id,
                        'name' => $discipline->name
                    ];
                }),
            ];
        });

        // Retornar a resposta JSON
        return ApiResponse::success([
            'school_id' => $school->id,
            'type_education' => $school->typeEducation->name,
            'levels' => $mappedLevels,
            'courses' => $courses,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/disciplines/levels/{level}",
     *     summary="Lista disciplinas de um nível específico",
     *     description="Recupera disciplinas vinculadas a um nível específico dentro de uma escola.",
     *     operationId="getLevelDisciplines",
     *     tags={"Discipline"},
     *     @OA\Parameter(
     *         name="school",
     *         in="path",
     *         description="ID da escola",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="level",
     *         in="path",
     *         description="ID do nível",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Disciplinas encontradas")
     * )
     */
    public function getLevelDisciplines(Level $level)
    {
        $school = Auth::user()->school;

        if (!$school) {
            return ApiResponse::error('Nenhuma escola associada ao usuário logado.', 404);
        }

        $disciplines = $level->disciplines()
            ->wherePivot('school_id', $school->id)
            ->get();

        return ApiResponse::success($disciplines, 200);
    }

    /**
     * @OA\Post(
     *     path="/disciplines/levels/{level}",
     *     summary="Adiciona uma disciplina a um nível",
     *     description="Associa uma disciplina a um nível específico de uma escola.",
     *     operationId="addLevelDiscipline",
     *     tags={"Discipline"},
     *     @OA\Parameter(
     *         name="school",
     *         in="path",
     *         description="ID da escola",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="level",
     *         in="path",
     *         description="ID do nível",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"discipline_id"},
     *             @OA\Property(property="discipline_id", type="string", format="uuid", description="ID da disciplina")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Disciplina associada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Disciplina associada com sucesso!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="O campo discipline_id é obrigatório.")
     *         )
     *     )
     * )
     */
    public function addLevelDiscipline(Request $request, Level $level)
    {
        $validated = $request->validate([
            'discipline_id' => 'required|exists:disciplines,id',
        ]);

        $school = Auth::user()->school;

        if (!$school) {
            return ApiResponse::error('Nenhuma escola associada ao usuário logado.', 404);
        }

        SchoolLevelDiscipline::firstOrCreate([
            'school_id' => $school->id,
            'level_id' => $level->id,
            'discipline_id' => $validated['discipline_id'],
        ]);

        return ApiResponse::success(['message' => 'Disciplina associada com sucesso!'], 201);
    }

    /**
     * @OA\Delete(
     *     path="/disciplines/{discipline}/levels/{level}/",
     *     summary="Remove uma disciplina de um nível",
     *     description="Remove a associação de uma disciplina com um nível específico de uma escola.",
     *     operationId="removeLevelDiscipline",
     *     tags={"Discipline"},
     *     @OA\Parameter(
     *         name="school",
     *         in="path",
     *         description="ID da escola",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="level",
     *         in="path",
     *         description="ID do nível",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="discipline",
     *         in="path",
     *         description="ID da disciplina",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Disciplina removida com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Disciplina removida com sucesso!")
     *         )
     *     )
     * )
     */
    public function removeLevelDiscipline(Level $level, Discipline $discipline)
    {
        $school = Auth::user()->school;

        if (!$school) {
            return ApiResponse::error('Nenhuma escola associada ao usuário logado.', 404);
        }

        SchoolLevelDiscipline::where('school_id', $school->id)
            ->where('level_id', $level->id)
            ->where('discipline_id', $discipline->id)
            ->delete();

        return ApiResponse::success(['message' => 'Disciplina removida com sucesso!'], 200);
    }

    /**
     * @OA\Post(
     *     path="/disciplines/courses/{course}",
     *     summary="Adiciona uma disciplina a um curso",
     *     description="Associa uma disciplina a um curso específico de uma escola.",
     *     operationId="addCourseDiscipline",
     *     tags={"Discipline"},
     *     @OA\Parameter(
     *         name="school",
     *         in="path",
     *         description="ID da escola",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         description="ID do curso",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"discipline_id"},
     *             @OA\Property(property="discipline_id", type="string", format="uuid", description="ID da disciplina")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Disciplina associada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Disciplina associada com sucesso!")
     *         )
     *     )
     * )
     */
    public function addCourseDiscipline(Request $request, Course $course)
    {
        $validated = $request->validate([
            'discipline_id' => 'required|exists:disciplines,id',
        ]);

        $school = Auth::user()->school;

        if (!$school) {
            return ApiResponse::error('Nenhuma escola associada ao usuário logado.', 404);
        }

        SchoolCourseDiscipline::firstOrCreate([
            'school_id' => $school->id,
            'course_id' => $course->id,
            'discipline_id' => $validated['discipline_id'],
        ]);

        return ApiResponse::success(['message' => 'Disciplina associada com sucesso!'], 201);
    }

    /**
     * @OA\Delete(
     *     path="/disciplines/{discipline}/courses/{course}",
     *     summary="Remove uma disciplina de um curso",
     *     description="Remove a associação de uma disciplina com um curso específico de uma escola.",
     *     operationId="removeCourseDiscipline",
     *     tags={"Discipline"},
     *     @OA\Parameter(
     *         name="school",
     *         in="path",
     *         description="ID da escola",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         description="ID do curso",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="discipline",
     *         in="path",
     *         description="ID da disciplina",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Disciplina removida com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Disciplina removida com sucesso!")
     *         )
     *     )
     * )
     */
    public function removeCourseDiscipline(Course $course, Discipline $discipline)
    {
        $school = Auth::user()->school;

        if (!$school) {
            return ApiResponse::error('Nenhuma escola associada ao usuário logado.', 404);
        }

        SchoolCourseDiscipline::where('school_id', $school->id)
            ->where('course_id', $course->id)
            ->where('discipline_id', $discipline->id)
            ->delete();

        return ApiResponse::success(['message' => 'Disciplina removida com sucesso!'], 200);
    }
}
