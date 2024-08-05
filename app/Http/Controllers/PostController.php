<?php

namespace App\Http\Controllers;

use App\Repositories\PostRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    use CrudTrait;

    protected $storeValidationRules = [
        'user_id' => 'required|exists:users,id',
        'title' => 'required|string|max:255',
        'body' => 'required|string',
    ];

    protected $updateValidationRules = [
        'user_id' => 'sometimes|required|exists:users,id',
        'title' => 'sometimes|required|string|max:255',
        'body' => 'sometimes|required|string',
    ];

    protected $uniqueFields = [];

    protected $resource = PostResource::class;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }
}
