<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="SetupStep",
 *     title="Setup Step",
 *     description="Modelo de uma etapa do setup da escola",
 *     @OA\Property(
 *         property="key",
 *         type="string",
 *         example="school_info",
 *         description="Identificador único da etapa"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="Informações da Escola",
 *         description="Nome da etapa"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         example="Configure as informações básicas da instituição",
 *         description="Descrição detalhada da etapa"
 *     ),
 *     @OA\Property(
 *         property="icon",
 *         type="string",
 *         example="ri-building-line",
 *         description="Ícone representativo da etapa"
 *     ),
 *     @OA\Property(
 *         property="order",
 *         type="integer",
 *         example=1,
 *         description="Ordem de exibição/execução da etapa"
 *     ),
 *     @OA\Property(
 *         property="required",
 *         type="boolean",
 *         example=true,
 *         description="Indica se a etapa é obrigatória"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"pending", "in_progress", "completed"},
 *         example="pending",
 *         description="Status atual da etapa"
 *     ),
 *     @OA\Property(
 *         property="completed",
 *         type="boolean",
 *         example=false,
 *         description="Indica se a etapa foi concluída"
 *     ),
 *     @OA\Property(
 *         property="completed_at",
 *         type="string",
 *         format="date-time",
 *         nullable=true,
 *         description="Data e hora de conclusão da etapa"
 *     ),
 *     @OA\Property(
 *         property="can_skip",
 *         type="boolean",
 *         example=false,
 *         description="Indica se a etapa pode ser pulada"
 *     ),
 *     @OA\Property(
 *         property="submitted_data",
 *         type="object",
 *         nullable=true,
 *         description="Dados enviados na conclusão da etapa"
 *     )
 * )
 */
class SetupStepSchema{}
/**
 * @OA\Schema(
 *     schema="SetupProgress",
 *     title="Setup Progress",
 *     description="Progresso geral do setup da escola",
 *     @OA\Property(
 *         property="total_steps",
 *         type="integer",
 *         example=8,
 *         description="Total de etapas do setup"
 *     ),
 *     @OA\Property(
 *         property="completed_steps",
 *         type="integer",
 *         example=3,
 *         description="Número de etapas concluídas"
 *     ),
 *     @OA\Property(
 *         property="progress_percentage",
 *         type="integer",
 *         example=37,
 *         description="Percentual de conclusão do setup"
 *     ),
 *     @OA\Property(
 *         property="current_step",
 *         type="string",
 *         example="school_info",
 *         description="Etapa atual do setup"
 *     ),
 *     @OA\Property(
 *         property="steps",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/SetupStep"),
 *         description="Lista de todas as etapas com seus estados"
 *     )
 * )
 */
class SetupProgressSchema {}