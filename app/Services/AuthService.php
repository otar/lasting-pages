<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function login(Request $request): void
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        $request->session()->regenerate();
    }

    /**
     * @param  array<string, string>  $validatedData
     */
    public function register(array $validatedData): User
    {
        $email = (string) $validatedData['email'];
        $password = (string) $validatedData['password'];

        $user = User::create([
            'name' => explode('@', $email)[0],
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        Auth::login($user);

        return $user;
    }

    public function logout(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
