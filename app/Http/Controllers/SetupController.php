<?php

namespace App\Http\Controllers;

use App\Services\SetupService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Setup",
 *     description="Endpoints for managing the school setup process"
 *    )
 */
class SetupController extends Controller
{
    protected $setupService;

    public function __construct(SetupService $setupService)
    {
        $this->setupService = $setupService;
    }

    /**
     * Check the setup status of the school.
     *
     * @OA\Get(
     *     path="/setup/status",
     *     tags={"Setup"},
     *     summary="Check the setup status",
     *     description="Returns whether the setup is required and the current progress of the school setup.",
     *     operationId="checkSetupStatus",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Setup status and progress",
     *         @OA\JsonContent(
     *             @OA\Property(property="setup_required", type="boolean", example=true),
     *             @OA\Property(
     *                 property="progress",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="step", type="string", example="informacoes"),
     *                     @OA\Property(property="completed", type="boolean", example=false)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function checkSetupStatus(Request $request)
    {
        $schoolId = $request->user()->school_id; // Assumindo que o usuário tem uma escola associada

        if ($this->setupService->isFirstLogin($schoolId)) {
            $this->setupService->initializeSetupSteps($schoolId);
        }

        return response()->json([
            'setup_required' => !$this->setupService->isSetupComplete($schoolId),
            'progress' => $this->setupService->getProgress($schoolId)
        ]);
    }

    /**
     * Complete a specific setup step.
     *
     * @OA\Post(
     *     path="/setup/step/{step}",
     *     tags={"Setup"},
     *     summary="Complete a setup step",
     *     description="Marks a specific step of the school setup process as completed.",
     *     operationId="completeStep",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="step",
     *         in="path",
     *         required=true,
     *         description="The name of the setup step to mark as completed",
     *         @OA\Schema(type="string", example="informacoes")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object", description="Data related to the step", example={"field": "value"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Step completed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Step completed successfully"),
     *             @OA\Property(
     *                 property="step",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="informacoes"),
     *                 @OA\Property(property="completed_at", type="string", format="date-time", example="2024-11-21T12:34:56Z")
     *             ),
     *             @OA\Property(
     *                 property="progress",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="step", type="string", example="informacoes"),
     *                     @OA\Property(property="completed", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation error details")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function completeStep(Request $request, string $step)
    {
        try {
            $schoolId = $request->user()->school_id;
            $stepData = $request->all();

            $completedStep = $this->setupService->markStepAsCompleted($schoolId, $step, $stepData);

            return response()->json([
                'message' => 'Step completed successfully',
                'step' => $completedStep,
                'progress' => $this->setupService->getProgress($schoolId)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
