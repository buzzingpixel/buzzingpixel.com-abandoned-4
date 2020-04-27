<?php

declare(strict_types=1);

namespace App\Stripe;

use Stripe\Charge;
use Stripe\Collection;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentMethod;
use Stripe\Stripe as StripeApi;
use function array_merge;
use function getenv;

class Stripe
{
    public function __construct()
    {
        StripeApi::setApiKey(
            (string) getenv('STRIPE_SECRET_KEY')
        );

        StripeApi::setApiVersion('2020-03-02');

        StripeApi::setAppInfo(
            'BuzzingPixel.com',
            null,
            'https://www.buzzingpixel.com'
        );
    }

    public function getApiKey() : string
    {
        return StripeApi::getApiKey();
    }

    /**
     * @param mixed[] $params
     * @param mixed[] $options
     *
     * @throws ApiErrorException
     */
    public function createCharge(
        array $params = [],
        array $options = []
    ) : Charge {
        $params = array_merge(
            ['currency' => 'usd'],
            $params,
        );

        return Charge::create(
            $params,
            $options,
        );
    }

    /**
     * @param mixed[] $options
     *
     * @throws ApiErrorException
     */
    public function retrieveCharge(
        string $id,
        array $options = []
    ) : Charge {
        return Charge::retrieve($id, $options);
    }

    /**
     * @param mixed[] $params
     * @param mixed[] $options
     *
     * @throws ApiErrorException
     */
    public function updateCharge(
        string $id,
        array $params = [],
        array $options = []
    ) : Charge {
        return Charge::update($id, $params, $options);
    }

    /**
     * @param mixed[] $params
     * @param mixed[] $options
     *
     * @throws ApiErrorException
     */
    public function allCharges(
        array $params = [],
        array $options = []
    ) : Collection {
        return Charge::all($params, $options);
    }

    /**
     * @param mixed[] $params
     * @param mixed[] $options
     *
     * @throws ApiErrorException
     */
    public function createCustomer(
        array $params = [],
        array $options = []
    ) : Customer {
        return Customer::create($params, $options);
    }

    /**
     * @param mixed[] $options
     *
     * @throws ApiErrorException
     */
    public function retrieveCustomer(
        string $id,
        array $options = []
    ) : Customer {
        return Customer::retrieve($id, $options);
    }

    /**
     * @param mixed[] $params
     * @param mixed[] $options
     *
     * @throws ApiErrorException
     */
    public function updateCustomer(
        string $id,
        array $params = [],
        array $options = []
    ) : Customer {
        return Customer::update($id, $params, $options);
    }

    /**
     * @param mixed[] $params
     * @param mixed[] $options
     *
     * @throws ApiErrorException
     */
    public function allCustomers(
        array $params = [],
        array $options = []
    ) : Collection {
        return Customer::all($params, $options);
    }

    /**
     * @param mixed[] $params
     * @param mixed[] $options
     *
     * @throws ApiErrorException
     */
    public function createPaymentMethod(
        array $params = [],
        array $options = []
    ) : PaymentMethod {
        return PaymentMethod::create($params, $options);
    }

    /**
     * @param mixed[] $options
     *
     * @throws ApiErrorException
     */
    public function retrievePaymentMethod(
        string $id,
        array $options = []
    ) : PaymentMethod {
        return PaymentMethod::retrieve($id, $options);
    }

    /**
     * @param mixed[] $params
     * @param mixed[] $options
     *
     * @throws ApiErrorException
     */
    public function updatePaymentMethod(
        string $id,
        array $params = [],
        array $options = []
    ) : PaymentMethod {
        return PaymentMethod::update($id, $params, $options);
    }

    /**
     * @param mixed[] $params
     * @param mixed[] $options
     *
     * @throws ApiErrorException
     */
    public function listAllPaymentMethodsForCustomer(
        array $params = [],
        array $options = []
    ) : Collection {
        return PaymentMethod::all($params, $options);
    }
}
