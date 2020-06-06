<?php

declare(strict_types=1);

namespace App\Http\Account\Profile;

use App\Content\Meta\MetaPayload;
use App\Http\StandardResponderConstructor;
use App\Users\Models\UserModel;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAccountProfileResponder extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke(UserModel $user) : ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/Account/ProfileView.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Edit Your Profile']
                ),
                'activeTab' => 'profile',
                'user' => $user,
            ]
        ));

        return $response;
    }
}
