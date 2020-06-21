<?php

declare(strict_types=1);

namespace App\Http\Account\PaymentMethods\Update;

use App\Factories\ValidationFactory;
use App\Payload\Payload;
use App\Users\Models\LoggedInUser;
use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as V;
use Safe\DateTimeImmutable;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class PostUpdatePaymentMethodAction
{
    private LoggedInUser $user;
    private UserApi $userApi;
    private ValidationFactory $validationFactory;
    private PostUpdatePaymentMethodResponder $responder;

    public function __construct(
        LoggedInUser $user,
        UserApi $userApi,
        ValidationFactory $validationFactory,
        PostUpdatePaymentMethodResponder $responder
    ) {
        $this->user              = $user;
        $this->userApi           = $userApi;
        $this->validationFactory = $validationFactory;
        $this->responder         = $responder;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $id = (string) $request->getAttribute('id');

        $card = $this->userApi->fetchUserCardById(
            $this->user->model(),
            $id
        );

        if ($card === null) {
            throw new HttpNotFoundException($request);
        }

        $post = (array) $request->getParsedBody();

        $data = [
            'expiration_month' => (string) ($post['expiration_date']['month'] ?? ''),
            'expiration_year' => (string) ($post['expiration_date']['year'] ?? ''),
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
                'expiration_month' => V::allOf(
                    V::notEmpty(),
                    V::numeric(),
                ),
                'expiration_year' => V::allOf(
                    V::notEmpty(),
                    V::numeric(),
                ),
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
                $id,
            );
        }

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

        if ($payload->getStatus() !== Payload::STATUS_UPDATED) {
            $result = $payload->getResult();

            $result['inputValues'] = $data;

            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_VALID,
                    $result,
                ),
                $id,
            );
        }

        return ($this->responder)($payload, $id);
    }
}
