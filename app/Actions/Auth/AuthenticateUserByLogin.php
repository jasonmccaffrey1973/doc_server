<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthenticateUserByLogin
{
    public function execute(string $login, string $password): ?User
    {
        $normalizedLogin = Str::lower(trim($login));

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$normalizedLogin])
            ->orWhereRaw('LOWER(username) = ?', [$normalizedLogin])
            ->first();

        if (! $user) {
            return null;
        }

        return Hash::check($password, $user->password) ? $user : null;
    }
}
