<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function gravatarUrl(User $user): string
    {
        $emailHash = hash('sha256', $user->email);

        return "https://gravatar.com/avatar/{$emailHash}?d=mp";
    }
}
