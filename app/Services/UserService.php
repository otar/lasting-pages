<?php

namespace App\Services;

use App\Models\User;
use Exception;

class UserService
{
    private const MAX_GRAVATAR_SIZE = 2048;

    public function gravatarUrl(User $user, string $default = 'mp', ?int $size = null): string
    {
        $emailHash = hash('sha256', $user->email);

        $params = [
            'd' => $default,
        ];

        if ($size !== null) {
            if ($size <= 0 || $size > self::MAX_GRAVATAR_SIZE) {
                throw new Exception('Gravatar size should be between 1 and '.self::MAX_GRAVATAR_SIZE.'.');
            }
            $params['s'] = $size;
        }

        $queryString = http_build_query($params);

        return "https://gravatar.com/avatar/{$emailHash}?{$queryString}";
    }
}
