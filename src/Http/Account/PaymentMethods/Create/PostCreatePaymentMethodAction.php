<?php

declare(strict_types=1);

namespace App\Http\Account\PaymentMethods\Create;

use App\Factories\ValidationFactory;
use App\Payload\Payload;
use App\Users\Models\LoggedInUser;
use App\Users\Models\UserCardModel;
use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as V;
use Safe\DateTimeImmutable;

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

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $post = (array) $request->getParsedBody();

        $data = [
            'card_number' => (string) ($post['card_number'] ?? ''),
            'expiration_month' => (string) ($post['expiration_date']['month'] ?? ''),
            'expiration_year' => (string) ($post['expiration_date']['year'] ?? ''),
            'cvc' => (string) ($post['cvc'] ?? ''),
            'name_on_card' => (string) ($post['name_on_card'] ?? ''),
            'address' => (string) ($post['address'] ?? ''),
            'address2' => (string) ($post['address2'] ?? ''),
            'country' => (string) ($post['country'] ?? ''),
            'postal_code' => (string) ($post['postal_code'] ?? ''),
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
                'cvc' => V::notEmpty(),
                'name_on_card' => V::notEmpty(),
                'address' => V::notEmpty(),
                'country' => V::notEmpty(),
                'postal_code' => V::notEmpty(),
                'nickname' => V::length(0, 255),
            ]
        );

        $validPostalCode = true;

        if ($data['postal_code'] !== '') {
            $validPostalCode = $this->userApi->validatePostalCode(
                $data['postal_code'],
                $data['country']
            );
        }

        if (! $validator->isValid() || ! $validPostalCode) {
            $errors = $validator->getErrors();

            if (
                isset($errors['expiration_month']) ||
                isset($errors['expiration_year'])
            ) {
                $errors['expiration_date'] = 'Valid expiration required';
            }

            if (! $validPostalCode) {
                /** @phpstan-ignore-next-line */
                $errors['postal_code'][] = 'Postal code is invalid';
            }

            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_VALID,
                    [
                        'message' => 'The data provided was invalid',
                        'inputMessages' => $errors,
                        'inputValues' => $data,
                    ],
                ),
            );
        }

        $card = new UserCardModel();

        $card->user = $this->user->model();

        $card->newCardNumber = $data['card_number'];

        $card->newCvc = $data['cvc'];

        $card->nickname = $data['nickname'];

        $card->nameOnCard = $data['name_on_card'];

        $card->address = $data['address'];

        $card->address2 = $data['address2'];

        $card->country = $data['country'];

        $card->postalCode = $data['postal_code'];

        $card->isDefault = $data['default'];

        $card->expiration = DateTimeImmutable::createFromFormat(
            'm-Y-d',
            $data['expiration_month'] . '-' . $data['expiration_year'] . '-02',
        );

        $this->userApi->fillCardModelFromPostalCode($card);

        $payload = $this->userApi->saveUserCard($card);

        if ($payload->getStatus() !== Payload::STATUS_CREATED) {
            $result = $payload->getResult();

            $result['inputValues'] = $data;

            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_VALID,
                    $result,
                ),
            );
        }

        return ($this->responder)($payload);
    }
}
