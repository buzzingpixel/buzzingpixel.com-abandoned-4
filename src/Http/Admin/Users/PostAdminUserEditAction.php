<?php

declare(strict_types=1);

namespace App\Http\Admin\Users;

use App\Payload\Payload;
use App\Users\UserApi;
use App\Utilities\SimpleValidator;
use DateTimeZone;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;
use function assert;
use function count;
use function is_array;

class PostAdminUserEditAction
{
    private PostAdminUserEditResponder $responder;
    private UserApi $userApi;

    public function __construct(
        PostAdminUserEditResponder $responder,
        UserApi $userApi
    ) {
        $this->responder = $responder;
        $this->userApi   = $userApi;
    }

    /**
     * @throws HttpNotFoundException
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $user = $this->userApi->fetchUserById(
            (string) $request->getAttribute('id')
        );

        if ($user === null) {
            throw new HttpNotFoundException($request);
        }

        $postData = $request->getParsedBody();
        assert(is_array($postData));

        $inputValues = [
            'is_admin' => ($postData['is_admin'] ?? '') === 'true',
            'email_address' => (string) ($postData['email_address'] ?? ''),
            'timezone' => (string) ($postData['timezone'] ?? ''),
            'first_name' => (string) ($postData['first_name'] ?? ''),
            'last_name' => (string) ($postData['last_name'] ?? ''),
            'display_name' => (string) ($postData['display_name'] ?? ''),
            'billing_name' => (string) ($postData['billing_name'] ?? ''),
            'billing_company' => (string) ($postData['billing_company'] ?? ''),
            'billing_phone' => (string) ($postData['billing_phone'] ?? ''),
            'billing_address' => (string) ($postData['billing_address'] ?? ''),
            'billing_country' => (string) ($postData['billing_country'] ?? ''),
            'billing_postal_code' => (string) ($postData['billing_postal_code'] ?? ''),
        ];

        $inputMessages = [];

        if (! SimpleValidator::email($inputValues['email_address'])) {
            $inputMessages['email_address'] = 'A valid email is required';
        }

        $userTimezone = new DateTimeZone('US/Central');

        if ($inputValues['timezone'] === '') {
            $inputMessages['timezone'] = 'Timezone is required';
        } else {
            try {
                $userTimezone = new DateTimeZone($inputValues['timezone']);
            } catch (Throwable $e) {
                $inputMessages['timezone'] = 'A valid timezone is required';
            }
        }

        if ($inputValues['billing_postal_code'] !== '' && $inputValues['billing_country'] === '') {
            $inputMessages['billing_country'] = 'If a postal code is supplied, a country must also be supplied';
        } elseif ($inputValues['billing_postal_code'] !== '') {
            $validPostalCode = $this->userApi->validatePostalCode(
                $inputValues['billing_postal_code'],
                $inputValues['billing_country']
            );

            if (! $validPostalCode) {
                $inputMessages['billing_postal_code'] = 'Postal code is invalid';
            }
        }

        if (count($inputMessages) > 0) {
            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_VALID,
                    [
                        'message' => 'There were errors with your submission',
                        'inputMessages' => $inputMessages,
                        'inputValues' => $inputValues,
                    ],
                ),
                $user->id,
            );
        }

        $user->isAdmin           = $inputValues['is_admin'];
        $user->emailAddress      = $inputValues['email_address'];
        $user->timezone          = $userTimezone;
        $user->firstName         = $inputValues['first_name'];
        $user->lastName          = $inputValues['last_name'];
        $user->displayName       = $inputValues['display_name'];
        $user->billingName       = $inputValues['billing_name'];
        $user->billingCompany    = $inputValues['billing_company'];
        $user->billingPhone      = $inputValues['billing_phone'];
        $user->billingAddress    = $inputValues['billing_address'];
        $user->billingCountry    = $inputValues['billing_country'];
        $user->billingPostalCode = $inputValues['billing_postal_code'];

        $this->userApi->fillModelFromPostalCode($user);

        $payload = $this->userApi->saveUser($user);

        if ($payload->getStatus() !== Payload::STATUS_UPDATED) {
            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_UPDATED,
                    ['message' => 'An unknown error occurred'],
                ),
                $user->id,
            );
        }

        return ($this->responder)(
            $payload,
            $user->id,
        );
    }
}
