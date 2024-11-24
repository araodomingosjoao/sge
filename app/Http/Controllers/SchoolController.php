<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\School;
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

}
