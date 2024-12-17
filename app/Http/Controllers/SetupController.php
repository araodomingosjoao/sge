<?php

namespace App\Http\Controllers;

use App\Services\SetupService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Setup",
 *     description="API endpoints para gerenciar o processo de configuração inicial da escola"
 * )
 */
class SetupController extends Controller
{
    protected $setupService;

    public function __construct(SetupService $setupService)
    {
        $this->setupService = $setupService;
    }

    /**
     * Verifica o status da configuração da escola.
     *
     * @OA\Get(
     *     path="/setup/status",
     *     tags={"Setup"},
     *     summary="Verifica o status da configuração",
     *     description="Retorna se a configuração é necessária e o progresso atual do setup da escola.",
     *     operationId="checkSetupStatus",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Status do setup e progresso",
     *         @OA\JsonContent(
     *             @OA\Property(property="setup_required", type="boolean", example=true),
     *             @OA\Property(
     *                 property="progress",
     *                 type="object",
     *                 @OA\Property(property="total_steps", type="integer", example=8),
     *                 @OA\Property(property="completed_steps", type="integer", example=3),
     *                 @OA\Property(property="progress_percentage", type="integer", example=37),
     *                 @OA\Property(property="current_step", type="string", example="school_info"),
     *                 @OA\Property(
     *                     property="steps",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="key", type="string", example="school_info"),
     *                         @OA\Property(property="name", type="string", example="Informações da Escola"),
     *                         @OA\Property(property="description", type="string", example="Configure as informações básicas"),
     *                         @OA\Property(property="icon", type="string", example="ri-building-line"),
     *                         @OA\Property(property="order", type="integer", example=1),
     *                         @OA\Property(property="required", type="boolean", example=true),
     *                         @OA\Property(property="status", type="string", example="pending"),
     *                         @OA\Property(property="completed", type="boolean", example=false),
     *                         @OA\Property(property="completed_at", type="string", format="date-time", nullable=true),
     *                         @OA\Property(property="can_skip", type="boolean", example=false)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function checkSetupStatus(Request $request)
    {
        $schoolId = $request->user()->school_id;
        
        $this->setupService->initializeSetupSteps($schoolId);

        return response()->json([
            'setup_required' => !$this->setupService->isSetupComplete($schoolId),
            'progress' => $this->setupService->getProgress($schoolId)
        ]);
    }

    /**
     * Completa uma etapa específica do setup.
     *
     * @OA\Post(
     *     path="/setup/step/{step}",
     *     tags={"Setup"},
     *     summary="Completa uma etapa do setup",
     *     description="Marca uma etapa específica do processo de configuração como concluída.",
     *     operationId="completeStep",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="step",
     *         in="path",
     *         required=true,
     *         description="Chave da etapa a ser completada",
     *         @OA\Schema(
     *             type="string",
     *             enum={"school_info","academic_periods","courses","subjects","classes","departments","staff_roles","final_review"}
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"data"},
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Etapa concluída com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Etapa concluída com sucesso")
     *         )
     *     )
     * )
     */
    public function completeStep(Request $request, string $step)
    {
        try {
            $schoolId = $request->user()->school_id;
            $stepData = $request->input('data', []);

            $completedStep = $this->setupService->markStepAsCompleted($schoolId, $step, $stepData);

            return response()->json([
                'message' => 'Etapa concluída com sucesso',
                'step' => $completedStep,
                'progress' => $this->setupService->getProgress($schoolId)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Pula uma etapa opcional do setup.
     *
     * @OA\Post(
     *     path="/setup/step/{step}/skip",
     *     tags={"Setup"},
     *     summary="Pula uma etapa opcional",
     *     description="Marca uma etapa opcional como pulada no processo de configuração.",
     *     operationId="skipStep",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="step",
     *         in="path",
     *         required=true,
     *         description="Chave da etapa a ser pulada",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Etapa pulada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Etapa pulada com sucesso"),
     *             @OA\Property(property="progress", ref="#/components/schemas/SetupProgress")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Etapa não pode ser pulada",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Esta etapa não pode ser pulada")
     *         )
     *     )
     * )
     */
    public function skipStep(Request $request, string $step)
    {
        try {
            $schoolId = $request->user()->school_id;
            
            $this->setupService->skipStep($schoolId, $step);

            return response()->json([
                'message' => 'Etapa pulada com sucesso',
                'progress' => $this->setupService->getProgress($schoolId)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Lista todas as etapas disponíveis do setup.
     *
     * @OA\Get(
     *     path="/setup/steps",
     *     tags={"Setup"},
     *     summary="Lista todas as etapas",
     *     description="Retorna todas as etapas disponíveis do processo de configuração.",
     *     operationId="listSteps",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de etapas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/SetupStep")
     *         )
     *     )
     * )
     */
    public function listSteps(Request $request)
    {
        $schoolId = $request->user()->school_id;
        return response()->json($this->setupService->getAvailableSteps($schoolId));
    }
}