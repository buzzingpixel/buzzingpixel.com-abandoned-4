<?php

declare(strict_types=1);

namespace App\Http\Account\PaymentMethods\Create;

use App\Factories\ValidationFactory;
use App\Payload\Payload;
use App\Users\Models\LoggedInUser;
use App\Users\Models\UserCardModel;
use App\Users\UserApi;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as V;
use function assert;

class PostCreatePaymentMethodAction
{
    private PostCreatePaymentMethodResponder $responder;
    private ValidationFactory $validationFactory;
    private LoggedInUser $user;
    private UserApi $userApi;

    public function __construct(
        PostCreatePaymentMethodResponder $responder,
        ValidationFactory $validationFactory,
        LoggedInUser $user,
        UserApi $userApi
    ) {
        $this->responder         = $responder;
        $this->validationFactory = $validationFactory;
        $this->user              = $user;
        $this->userApi           = $userApi;
    }

    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $post = (array) $request->getParsedBody();

        $data = [
            'card_number' => (string) ($post['card_number'] ?? ''),
            'expiration_month' => (string) ($post['expiration_date']['month'] ?? ''),
            'expiration_year' => (string) ($post['expiration_date']['year'] ?? ''),
            'default' => ($post['default'] ?? '') === 'true',
            'nickname' => (string) ($post['nickname'] ?? ''),
        ];

        $validator = ($this->validationFactory)([
            'creditCard' => 'Value must be a valid credit card number',
            'notEmpty' => 'Value must not be empty',
            'numeric' => 'Value must be numeric',
            'length' => 'Value must not be more than {{maxValue}} characters',
        ]);

        $validator->validate(
            $data,
            [
                'card_number' => V::creditCard(),
                'expiration_month' => V::allOf(
                    V::notEmpty(),
                    V::numeric(),
                ),
                'expiration_year' => V::allOf(
                    V::notEmpty(),
                    V::numeric(),
                ),
                'nickname' => V::length(0, 255),
            ]
        );

        if (! $validator->isValid()) {
            $errors = $validator->getErrors();

            if (isset($errors['expiration_month']) ||
                isset($errors['expiration_year'])
            ) {
                $errors['expiration_date'] = 'Valid expiration required';
            }

            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_VALID,
                    [
                        'message' => 'The data provided was invalid',
                        'inputMessages' => $errors,
                    ],
                ),
            );
        }

        $card = new UserCardModel();

        $card->user = $this->user->model();

        $card->nickname = $data['nickname'];

        $card->newCardNumber = $data['card_number'];

        $card->isDefault = $data['default'];

        $expiration = DateTimeImmutable::createFromFormat(
            'm-Y-d',
            $data['expiration_month'] . '-' . $data['expiration_year'] . '-02',
        );

        assert($expiration instanceof DateTimeImmutable);

        $card->expiration = $expiration;

        $payload = $this->userApi->saveUserCard($card);

        return ($this->responder)($payload);
    }
}
