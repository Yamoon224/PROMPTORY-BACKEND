<?php

namespace App\Domains\Users\Http\Controllers;

use App\Domains\Auth\Http\Resources\AuthResource;
use App\Domains\Users\Http\Requests\UpdatePasswordRequest;
use App\Domains\Users\Http\Requests\UpdateProfileRequest;
use App\Domains\Users\Services\ProfileService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/** Modification du compte par son propre titulaire (voir `ProfileService`). */
class ProfileController extends Controller
{
    public function __construct(private readonly ProfileService $profile) {}

    public function update(UpdateProfileRequest $request): AuthResource
    {
        return new AuthResource($this->profile->updateProfile($request->user(), $request->validated()));
    }

    public function updatePassword(UpdatePasswordRequest $request): Response
    {
        $this->profile->updatePassword(
            $request->user(),
            $request->string('current_password')->toString(),
            $request->string('password')->toString(),
        );

        return response()->noContent();
    }
}
