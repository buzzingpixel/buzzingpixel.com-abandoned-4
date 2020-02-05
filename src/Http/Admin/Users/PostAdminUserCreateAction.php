<?php

declare(strict_types=1);

namespace App\Http\Admin\Users;

use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\Services\SaveUser;
use App\Users\UserApi;
use App\Utilities\SimpleValidator;
use DateTimeZone;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use function count;

class PostAdminUserCreateAction
{
    /** @var PostAdminUserCreateResponder */
    private $responder;
    /** @var UserApi */
    private $userApi;

    public function __construct(
        PostAdminUserCreateResponder $responder,
        UserApi $userApi
    ) {
        $this->responder = $responder;
        $this->userApi   = $userApi;
    }

    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $postData = $request->getParsedBody();

        $inputValues = [
            'is_admin' => ($postData['is_admin'] ?? '') === 'true',
            'email_address' => $postData['email_address'] ?? '',
            'password' => $postData['password'] ?? '',
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

        if (! SimpleValidator::password($inputValues['password'])) {
            $inputMessages['password'] = 'Password must be at least ' .
                SaveUser::MIN_PASSWORD_LENGTH . ' characters';
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
            );
        }

        $userModel = new UserModel([
            'isAdmin' => $inputValues['is_admin'],
            'emailAddress' => $inputValues['email_address'],
            'newPassword' => $inputValues['password'],
            'timezone' => $userTimezone,
            'firstName' => $inputValues['first_name'],
            'lastName' => $inputValues['last_name'],
            'displayName' => $inputValues['display_name'],
            'billingName' => $inputValues['billing_name'],
            'billingCompany' => $inputValues['billing_company'],
            'billingPhone' => $inputValues['billing_phone'],
            'billingAddress' => $inputValues['billing_address'],
            'billingCountry' => $inputValues['billing_country'],
            'billingPostalCode' => $inputValues['billing_postal_code'],
        ]);

        $this->userApi->fillModelFromPostalCode($userModel);

        $payload = $this->userApi->saveUser($userModel);

        if ($payload->getStatus() !== Payload::STATUS_CREATED) {
            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_CREATED,
                    ['message' => 'An unknown error occurred'],
                ),
            );
        }

        return ($this->responder)($payload);
    }
}