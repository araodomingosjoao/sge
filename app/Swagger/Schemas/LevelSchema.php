<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Level",
 *     type="object",
 *     required={"name", "type_education_id", "year"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="1º Classe"),
 *     @OA\Property(property="type_education_id", type="integer", example=1),
 *     @OA\Property(property="year", type="integer", example=2024)
 * )
 */
class LevelSchema {}
