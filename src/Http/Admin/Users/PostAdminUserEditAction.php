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
use function count;

class PostAdminUserEditAction
{
    /** @var PostAdminUserEditResponder */
    private $responder;
    /** @var UserApi */
    private $userApi;

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

        $inputValues = [
            'is_admin' => ($postData['is_admin'] ?? '') === 'true',
            'email_address' => $postData['email_address'] ?? '',
            'timezone' => $postData['timezone'] ?? '',
            'first_name' => $postData['first_name'] ?? '',
            'last_name' => $postData['last_name'] ?? '',
            'display_name' => $postData['display_name'] ?? '',
            'billing_name' => $postData['billing_name'] ?? '',
            'billing_company' => $postData['billing_company'] ?? '',
            'billing_phone' => $postData['billing_phone'] ?? '',
            'billing_address' => $postData['billing_address'] ?? '',
            'billing_country' => $postData['billing_country'] ?? '',
            'billing_postal_code' => $postData['billing_postal_code'] ?? '',
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

        if ($postData['billing_postal_code'] !== '' && $postData['billing_country'] === '') {
            $inputMessages['billing_country'] = 'If a postal code is supplied, a country must also be supplied';
        } elseif ($postData['billing_postal_code']) {
            $validPostalCode = $this->userApi->validatePostalCode(
                $postData['billing_postal_code'],
                $postData['billing_country']
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
                $user->getId(),
            );
        }

        $user->setIsAdmin($inputValues['is_admin']);
        $user->setEmailAddress($inputValues['email_address']);
        $user->setTimezone($userTimezone);
        $user->setFirstName($inputValues['first_name']);
        $user->setLastName($inputValues['last_name']);
        $user->setDisplayName($inputValues['display_name']);
        $user->setBillingName($inputValues['billing_name']);
        $user->setBillingCompany($inputValues['billing_company']);
        $user->setBillingPhone($inputValues['billing_phone']);
        $user->setBillingAddress($inputValues['billing_address']);
        $user->setBillingCountry($inputValues['billing_country']);
        $user->setBillingPostalCode($inputValues['billing_postal_code']);

        $this->userApi->fillModelFromPostalCode($user);

        $payload = $this->userApi->saveUser($user);

        if ($payload->getStatus() !== Payload::STATUS_UPDATED) {
            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_UPDATED,
                    ['message' => 'An unknown error occurred'],
                ),
                $user->getId(),
            );
        }

        return ($this->responder)(
            $payload,
            $user->getId(),
        );
    }
}
