<?php

namespace App\SimpsonsQuotes;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class Authenticator
{
    protected User $userModel;
    protected Hash $hasher;

    public function __construct(User $userModel, Hash $hasher)
    {
        $this->userModel = $userModel;
        $this->hasher = $hasher;

    }

    public function getLoginToken(string $email, string $password): array
    {
        $user = $this->userModel::where('email', $email)->first();

        if (!$user || !$this->hasher::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('spa_token')->plainTextToken;

        return ['token' => $token];
    }
}
