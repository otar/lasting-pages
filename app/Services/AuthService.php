<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * @SuppressWarnings("BooleanArgumentFlag")
     */
    public function login(string $email, string $password, bool $remember = false): void
    {
        $credentials = [
            'email' => $email,
            'password' => $password,
        ];

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        Session::regenerate();
    }

    public function register(string $email, string $password): User
    {
        $user = User::create([
            'name' => explode('@', $email)[0],
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        Auth::login($user);

        return $user;
    }

    public function logout(): void
    {
        Auth::logout();

        Session::invalidate();
        Session::regenerateToken();
    }
}
