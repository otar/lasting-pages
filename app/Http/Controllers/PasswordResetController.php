<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    private const PASSWORD_RESET_TOKEN_EXPIRY_MINUTES = 60;

    public function showLinkRequestForm(): View
    {
        return view('forgot-password');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        // Always send the reset link attempt, but always return the same message
        Password::sendResetLink($request->only('email'));

        return back()->with('status',
            'If your email is registered, you will be sent a password reset link which is valid for 1 hour.'
        );
    }

    public function showResetForm(Request $request, string $token): View
    {
        $email = $request->email;
        $isExpired = false;
        $recentEmail = null;

        // Check if token is expired by trying to find a valid user with this token
        if ($email) {
            /** @var \stdClass|null $tokenRecord */
            $tokenRecord = DB::table('password_reset_tokens')
                ->where('email', $email)
                ->where('token', hash('sha256', $token))
                ->first();

            if (! $tokenRecord) {
                // Token doesn't exist - consider it expired
                $isExpired = true;
            } elseif (isset($tokenRecord->created_at)) {
                /** @var string $tokenRecordCreatedAt */
                $tokenRecordCreatedAt = $tokenRecord->created_at;
                $tokenRecordCreatedAtDate = Carbon::parse($tokenRecordCreatedAt);

                if ($tokenRecordCreatedAtDate->addMinutes(self::PASSWORD_RESET_TOKEN_EXPIRY_MINUTES)->isPast()) {
                    $isExpired = true;
                }
            }

            // If expired, check for recent reset requests (within 24 hours)
            if ($isExpired) {
                $recentRequest = DB::table('password_reset_tokens')
                    ->where('email', $email)
                    ->where('created_at', '>=', now()->subHours(24))
                    ->first();

                if ($recentRequest) {
                    $recentEmail = $email;
                }
            }
        }

        return view('reset-password', [
            'token' => $token,
            'email' => $email,
            'isExpired' => $isExpired,
            'recentEmail' => $recentEmail,
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        /** @var string $status */
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (\App\Models\User $user, string $password): void {
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
