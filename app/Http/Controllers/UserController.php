<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function me()
    {
        return new UserResource(auth()->user());
    }
}
