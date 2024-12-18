<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Course;
use App\Models\Discipline;
use App\Models\Level;
use App\Repositories\SchoolDisciplineRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Discipline",
 *     description="API Endpoints para gerenciamento de disciplinas"
 * )
 */
class DisciplineController extends Controller
{
    protected $repository;

    public function __construct(SchoolDisciplineRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *     path="/disciplines",
     *     summary="Lista estrutura completa de disciplinas da escola",
     *     description="Retorna todas as disciplinas organizadas por níveis e cursos",
     *     tags={"Discipline"},
     *     security={{ "sanctum": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Estrutura de disciplinas",
     *         @OA\JsonContent(
     *             @OA\Property(property="school_id", type="string", format="uuid"),
     *             @OA\Property(property="type_education", type="string"),
     *             @OA\Property(property="levels", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="year", type="integer"),
     *                     @OA\Property(property="disciplines", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="string", format="uuid"),
     *                             @OA\Property(property="name", type="string"),
     *                             @OA\Property(property="course", type="object", nullable=true,
     *                                 @OA\Property(property="id", type="string", format="uuid"),
     *                                 @OA\Property(property="name", type="string")
     *                             )
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="courses", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="disciplines", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="string", format="uuid"),
     *                             @OA\Property(property="name", type="string")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Escola não encontrada")
     * )
     */
    public function index(): JsonResponse
    {
        $school = Auth::user()->school;

        if (!$school) {
            return ApiResponse::error('Nenhuma escola associada ao usuário logado.', 404);
        }

        $data = $school->typeEducation->name === 'Ensino Fundamental'
            ? $this->repository->getFundamentalStructure($school)
            : $this->repository->getHighSchoolStructure($school);

        return ApiResponse::success($data);
    }

    /**
     * @OA\Get(
     *     path="/disciplines/all",
     *     summary="Lista todas as disciplinas disponíveis",
     *     tags={"Discipline"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Termo de busca",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Itens por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de disciplinas",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Discipline"))
     *     )
     * )
     */
    public function all(Request $request): JsonResponse
    {
        $disciplines = $this->repository->paginateWithFiltersAndSort(
            [],
            $request->search,
            $request->per_page ?? 15
        );
        return ApiResponse::success($disciplines);
    }

    /**
     * @OA\Get(
     *     path="/disciplines/by-level/{level}",
     *     summary="Lista disciplinas de um nível específico",
     *     tags={"Discipline"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="level",
     *         in="path",
     *         description="ID do nível",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de disciplinas do nível",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Discipline"))
     *     )
     * )
     */
    public function getByLevel(Level $level): JsonResponse
    {
        $disciplines = $this->repository->getByLevel($level->id);
        return ApiResponse::success($disciplines);
    }

    /**
     * @OA\Get(
     *     path="/disciplines/by-course/{course}",
     *     summary="Lista disciplinas de um curso específico",
     *     tags={"Discipline"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         description="ID do curso",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de disciplinas do curso",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Discipline"))
     *     )
     * )
     */
    public function getByCourse(Course $course): JsonResponse
    {
        $disciplines = $this->repository->getByCourse($course->id);
        return ApiResponse::success($disciplines);
    }

    /**
     * @OA\Post(
     *     path="/disciplines/levels/{level}/batch",
     *     summary="Associa múltiplas disciplinas a um nível",
     *     tags={"Discipline"},
     *     security={{ "sanctum": {} }},
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
     *             required={"discipline_ids"},
     *             @OA\Property(property="discipline_ids", type="array",
     *                 @OA\Items(type="string", format="uuid")
     *             ),
     *             @OA\Property(property="course_id", type="string", format="uuid", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Disciplinas associadas com sucesso"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function associateWithLevel(Request $request, Level $level): JsonResponse
    {
        $validated = $request->validate([
            'discipline_ids' => 'required|array',
            'discipline_ids.*' => 'exists:disciplines,id',
            'course_id' => 'nullable|exists:courses,id'
        ]);

        $this->repository->associateWithLevel(
            $level->id,
            $validated['discipline_ids'],
            $validated['course_id'] ?? null
        );

        return ApiResponse::success(['message' => 'Disciplinas associadas com sucesso'], 201);
    }

    /**
     * @OA\Post(
     *     path="/disciplines/courses/{course}/batch",
     *     summary="Associa múltiplas disciplinas a um curso",
     *     tags={"Discipline"},
     *     security={{ "sanctum": {} }},
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
     *             required={"discipline_ids", "level_ids"},
     *             @OA\Property(property="discipline_ids", type="array",
     *                 @OA\Items(type="string", format="uuid")
     *             ),
     *             @OA\Property(property="level_ids", type="array",
     *                 @OA\Items(type="string", format="uuid")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Disciplinas associadas com sucesso"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function associateWithCourse(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'discipline_ids' => 'required|array',
            'discipline_ids.*' => 'exists:disciplines,id',
            'level_ids' => 'required|array',
            'level_ids.*' => 'exists:levels,id'
        ]);

        $this->repository->associateWithCourse(
            $course->id,
            $validated['discipline_ids'],
            $validated['level_ids']
        );

        return ApiResponse::success(['message' => 'Disciplinas associadas com sucesso'], 201);
    }

    /**
     * @OA\Delete(
     *     path="/disciplines/levels/{level}/{discipline}",
     *     summary="Remove uma disciplina de um nível",
     *     tags={"Discipline"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="level",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="discipline",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Disciplina removida com sucesso"),
     *     @OA\Response(response=404, description="Registro não encontrado")
     * )
     */
    public function dissociateFromLevel(Level $level, Discipline $discipline): JsonResponse
    {
        $this->repository->dissociateFromLevel($level->id, $discipline->id);
        return ApiResponse::success(['message' => 'Disciplina removida com sucesso']);
    }

    /**
     * @OA\Delete(
     *     path="/disciplines/courses/{course}/{discipline}",
     *     summary="Remove uma disciplina de um curso",
     *     tags={"Discipline"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="discipline",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Disciplina removida com sucesso"),
     *     @OA\Response(response=404, description="Registro não encontrado")
     * )
     */
    public function dissociateFromCourse(Course $course, Discipline $discipline): JsonResponse
    {
        $this->repository->dissociateFromCourse($course->id, $discipline->id);
        return ApiResponse::success(['message' => 'Disciplina removida com sucesso']);
    }
}
