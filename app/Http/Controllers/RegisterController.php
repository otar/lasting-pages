<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RegisterController
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function showRegistrationForm(): View
    {
        return view('register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:12|confirmed',
        ]);

        $this->authService->register(
            email: $request->string('email')->toString(),
            password: $request->string('password')->toString()
        );

        return redirect()->route('dashboard');
    }
}
