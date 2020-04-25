<?php

declare(strict_types=1);

namespace App\Http\Account\RequestPasswordReset\Msg;

use App\Content\Meta\MetaPayload;
use App\Http\StandardResponderConstructor;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetMessageAction extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke() : ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twigEnvironment->render(
            'MessageOnly.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Password Reset']
                ),
                'message' => 'If that email address is associated with an' .
                    " account and you haven't requested a reset more than 5" .
                    " times in the last 2 hours, we'll send password reset" .
                    ' instructions to that email address.',
            ]
        ));

        return $response;
    }
}
