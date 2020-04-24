<?php

declare(strict_types=1);

namespace App\Http\Account\ResetPasswordWithToken;

use App\Users\Models\UserModel;
use Psr\Http\Message\ResponseInterface;
use function dd;

class ResetPasswordWithTokenResponder
{
    public function __invoke(UserModel $user) : ResponseInterface
    {
        // TODO: Implement __invoke() method
        dd($user);
    }
}
