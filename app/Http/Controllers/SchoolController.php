<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\School\SchoolResource;
use App\Models\AcademicYear;
use App\Models\Category;
use App\Models\School;
use App\Models\TypeEducation;
use App\Repositories\SchoolRepository;
use App\Traits\CrudTrait;
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
    use CrudTrait;

    protected $storeValidationRules = [];

    protected $updateValidationRules = [
        'type_education_id' => 'required|numeric|exists:type_educations,id',
        'category_id' => 'required_if:type_education_id,4|string|exists:categories,id',
        'school_name' => 'required|string|max:255',
        'email' => 'nullable|email',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:100',
        'state' => 'nullable|string|max:100',
        'country' => 'nullable|string|max:100',
        'postal_code' => 'nullable|string|max:20',
        'website' => 'nullable|url|max:255',
        'founded_year' => 'nullable|numeric|min:1800',
        'registration_number' => 'nullable|string|max:100',
    ];

    protected $uniqueFields = [];

    protected $resource = SchoolResource::class;
    protected $resourceDetails = SchoolResource::class;

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
            'optionsCategory' => Category::all(),
            'optionsTypeEducation' => TypeEducation::all()
        ]);
    }
}
