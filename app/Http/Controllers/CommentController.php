<?php

namespace App\Http\Controllers;

use App\Repositories\CommentRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    use CrudTrait;

    protected $storeValidationRules = [
        'post_id' => 'required|exists:posts,id',
        'user_id' => 'required|exists:users,id',
        'body' => 'required|string',
    ];

    protected $updateValidationRules = [
        'post_id' => 'sometimes|required|exists:posts,id',
        'user_id' => 'sometimes|required|exists:users,id',
        'body' => 'sometimes|required|string',
    ];
    
    protected $resource = CommentResource::class;

    public function __construct(CommentRepository $repository)
    {
        $this->repository = $repository;
    }
}
