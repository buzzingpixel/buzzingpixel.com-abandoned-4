<?php

declare(strict_types=1);

namespace App\Http\Cart;

use App\Cart\CartApi;
use App\Users\Models\LoggedInUser;
use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpException;
use Throwable;

use function assert;
use function is_array;

class PostCartAction
{
    private LoggedInUser $user;
    private UserApi $userApi;
    private CartApi $cartApi;
    private PostCartResponder $responder;

    public function __construct(
        LoggedInUser $user,
        UserApi $userApi,
        CartApi $cartApi,
        PostCartResponder $responder
    ) {
        $this->user      = $user;
        $this->userApi   = $userApi;
        $this->cartApi   = $cartApi;
        $this->responder = $responder;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $post = $request->getParsedBody();

        assert(is_array($post));

        $cardId = (string) ($post['card'] ?? '');

        $card = $this->userApi->fetchUserCardById(
            $this->user->model(),
            $cardId,
        );

        if ($card === null) {
            throw new HttpException(
                $request,
                'Invalid card on post',
            );
        }

        return ($this->responder)($this->cartApi->processCartOrder(
            $this->cartApi->fetchCurrentUserCart(),
            $card
        ));
    }
}
