<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="User data response",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="first_name", type="string", example="John"),
 *     @OA\Property(property="last_name", type="string", example="Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 *     @OA\Property(property="phone", type="string", example="987654321"),
 *     @OA\Property(property="profile_picture_path", type="string"),
 *     @OA\Property(property="status", type="string", example="active"),
 * )
 */
class UserSchema {}
