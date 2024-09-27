<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use CrudTrait;

    protected $storeValidationRules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone_number' => 'required|string|unique:users,phone_number',
        'password' => [
            'required',
            'string',
            'min:6',            
            'regex:/[a-z]/',    
            'regex:/[A-Z]/',    
            'regex:/[0-9]/',    
            'regex:/[@$!%*#?&]/'
        ],
    ];
    protected $updateValidationRules = [
        'name' => 'sometimes|required|string|max:255',
        'email' => 'sometimes|required|email',
        'phone_number' => 'sometimes|string|unique:users,phone_number',
        'password' => [
            'sometimes',
            'string',
            'min:6',            
            'regex:/[a-z]/',    
            'regex:/[A-Z]/',    
            'regex:/[0-9]/',    
            'regex:/[@$!%*#?&]/'
        ],
    ];

    protected $uniqueFields = [
        'email' => 'users',
        'phone_number' => 'users'
    ];

    protected $resource = UserResource::class;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }
}
