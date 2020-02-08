<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Users\Models\UserModel;
use GuzzleHttp\Client;
use Throwable;
use function assert;
use function is_array;
use function mb_strtoupper;
use function Safe\json_decode;

class PostalCodeService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /** @var array<string, mixed> */
    private array $apiCalls = [];

    /**
     * @return array<string, mixed>
     *
     * @psalm-suppress MixedInferredReturnType
     */
    private function makeApiCall(string $postalCode, string $alpha2Country) : array
    {
        $codeUpper = mb_strtoupper($alpha2Country);

        $key = $postalCode . '-' . $codeUpper;

        if (isset($this->apiCalls[$key])) {
            /** @psalm-suppress MixedReturnStatement */
            return $this->apiCalls[$key];
        }

        try {
            $response = $this->client->get(
                'http://api.zippopotam.us/' . $codeUpper . '/' . $postalCode
            );

            /** @psalm-suppress MixedAssignment */
            $json = json_decode(
                $response->getBody()->getContents(),
                true
            );
        } catch (Throwable $e) {
            $json = [];
        }

        assert(is_array($json));

        $this->apiCalls[$key] = $json;

        /** @psalm-suppress MixedReturnTypeCoercion */
        return $json;
    }

    public function validatePostalCode(string $postalCode, string $alpha2Country) : bool
    {
        $codeUpper = mb_strtoupper($alpha2Country);

        $json = $this->makeApiCall(
            $postalCode,
            $codeUpper
        );

        /** @psalm-suppress MixedAssignment */
        $jsonCountry = $json['country abbreviation'] ?? null;

        return $jsonCountry === $codeUpper;
    }

    public function fillModelFromPostalCode(UserModel $model) : void
    {
        $postalCode = $model->billingPostalCode;

        $countryCode = $model->billingCountry;

        if (! $this->validatePostalCode($postalCode, $countryCode)) {
            return;
        }

        $json = $this->makeApiCall($postalCode, $countryCode);

        $place =  (array) ($json['places'][0] ?? []);

        $model->billingCity = (string) ($place['place name'] ?? '');

        $model->billingStateAbbr = (string) ($place['state abbreviation'] ?? '');
    }
}
