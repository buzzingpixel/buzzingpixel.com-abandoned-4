<?php

declare(strict_types=1);

namespace App\Http\Account\ResetPasswordWithToken;

use App\Content\Meta\MetaPayload;
use App\Http\StandardResponderConstructor;
use App\Users\Models\UserModel;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetResetPasswordWithTokenResponder extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke(UserModel $user, string $token) : ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twigEnvironment->render(
            'Account/ResetPassword.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Reset your Password']
                ),
                'user' => $user,
                'token' => $token,
            ]
        ));

        return $response;
    }
}
