<?php

namespace App\Http\Controllers;

use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetController
{
    public function __construct(
        private readonly PasswordResetService $passwordResetService
    ) {}

    public function showLinkRequestForm(): View
    {
        return view('forgot-password');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->input('email');
        assert(is_string($email));
        $message = $this->passwordResetService->sendResetLink($email);

        return back()->with('status', $message);
    }

    public function showResetForm(Request $request, string $token): View
    {
        $email = $request->email;
        assert(is_string($email) || is_null($email));
        $data = $this->passwordResetService->getResetFormData($token, $email);

        return view('reset-password', $data);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:12|confirmed',
        ]);

        $status = $this->passwordResetService->resetPassword(
            email: $request->string('email')->toString(),
            password: $request->string('password')->toString(),
            passwordConfirmation: $request->string('password_confirmation')->toString(),
            token: $request->string('token')->toString()
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
