<?php
namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Discipline",
 *     title="Discipline",
 *     @OA\Property(
 *         property="id",
 *         type="string",
 *         format="uuid",
 *         description="ID único da disciplina"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nome da disciplina"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         nullable=true,
 *         description="Descrição da disciplina"
 *     ),
 *     @OA\Property(
 *         property="workload",
 *         type="integer",
 *         nullable=true,
 *         description="Carga horária da disciplina"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="datetime",
 *         description="Data de criação"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="datetime",
 *         description="Data de atualização"
 *     ),
 *     @OA\Property(
 *         property="deleted_at",
 *         type="string",
 *         format="datetime",
 *         nullable=true,
 *         description="Data de exclusão"
 *     ),
 *     @OA\Property(
 *         property="levels",
 *         type="array",
 *         description="Níveis associados à disciplina",
 *         @OA\Items(ref="#/components/schemas/Level")
 *     ),
 *     @OA\Property(
 *         property="courses",
 *         type="array",
 *         description="Cursos associados à disciplina",
 *         @OA\Items(ref="#/components/schemas/Course")
 *     )
 * )
 */
class DisciplineSchema {}