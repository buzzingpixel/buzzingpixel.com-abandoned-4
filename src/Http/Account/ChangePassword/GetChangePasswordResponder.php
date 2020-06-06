<?php

declare(strict_types=1);

namespace App\Http\Account\ChangePassword;

use App\Content\Meta\MetaPayload;
use App\Http\StandardResponderConstructor;
use App\Users\Models\UserModel;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetChangePasswordResponder extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke(UserModel $user) : ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/Account/ChangePassword.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Change Your Password']
                ),
                'activeTab' => 'change-password',
                'user' => $user,
            ]
        ));

        return $response;
    }
}
