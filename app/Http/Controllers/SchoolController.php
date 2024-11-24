<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\TypeEducation;
use App\Repositories\SchoolRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Schools",
 *     description="API Endpoints for Schools"
 * )
 */
class SchoolController extends Controller
{
    protected $repository;

    public function __construct(SchoolRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *     path="/options",
     *     summary="Listar todas as opções",
     *     tags={"Schools"},
     *     @OA\Response(
     *         response=200,
     *         description="Get all options successfully",
     *         @OA\JsonContent(
     *             
     *         )
     *     )
     * )
     */
    public function options()
    {
        return ApiResponse::success([
            'optionsTypeEducation' => TypeEducation::all()
        ]);
    }
}
