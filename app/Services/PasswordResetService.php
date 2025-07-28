<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

class PasswordResetService
{
    private const PASSWORD_RESET_TOKEN_EXPIRY_MINUTES = 60;

    public function sendResetLink(string $email): string
    {
        Password::sendResetLink(['email' => $email]);

        return 'If your email is registered, you will be sent a password reset link which is valid for 1 hour.';
    }

    /**
     * @return array<string, mixed>
     */
    public function getResetFormData(string $token, ?string $email): array
    {
        $isExpired = false;
        $recentEmail = null;

        if ($email) {
            $tokenRecord = DB::table('password_reset_tokens')
                ->where('email', $email)
                ->where('token', hash('sha256', $token))
                ->first();

            if (! $tokenRecord) {
                $isExpired = true;
            } elseif (property_exists($tokenRecord, 'created_at') && isset($tokenRecord->created_at)) {
                /** @var string $createdAt */
                $createdAt = $tokenRecord->created_at;
                $tokenRecordCreatedAtDate = Carbon::parse($createdAt);

                if ($tokenRecordCreatedAtDate->addMinutes(self::PASSWORD_RESET_TOKEN_EXPIRY_MINUTES)->isPast()) {
                    $isExpired = true;
                }
            }

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

        return [
            'token' => $token,
            'email' => $email,
            'isExpired' => $isExpired,
            'recentEmail' => $recentEmail,
        ];
    }

    public function resetPassword(string $email, string $password, string $passwordConfirmation, string $token): string
    {
        /** @var string $status */
        $status = Password::reset(
            [
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
                'token' => $token,
            ],
            function (\App\Models\User $user, string $password): void {
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        return $status;
    }
}
