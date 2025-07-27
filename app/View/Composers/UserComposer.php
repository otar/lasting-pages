<?php

namespace App\View\Composers;

use App\Services\UserService;
use Illuminate\View\View;

class UserComposer
{
    public function __construct(
        private UserService $userService
    ) {}

    public function compose(View $view): void
    {
        $user = auth()->user();

        if ($user) {
            $view->with([
                'userEmail' => $user->email,
                'userAvatar' => $this->userService->gravatarUrl($user, size: 32),
            ]);
        }
    }
}
