<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Users\Models\UserModel;
use GuzzleHttp\Client;
use Throwable;
use function mb_strtoupper;
use function Safe\json_decode;

class PostalCodeService
{
    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /** @var array<string, mixed> */
    private $apiCalls = [];

    /**
     * @return array<string, mixed>
     */
    private function makeApiCall(string $postalCode, string $alpha2Country) : array
    {
        $codeUpper = mb_strtoupper($alpha2Country);

        $key = $postalCode . '-' . $codeUpper;

        if (isset($this->apiCalls[$key])) {
            return $this->apiCalls[$key];
        }

        try {
            $response = $this->client->get(
                'http://api.zippopotam.us/' . $codeUpper . '/' . $postalCode
            );

            $json = json_decode(
                $response->getBody()->getContents(),
                true
            );
        } catch (Throwable $e) {
            $json = [];
        }

        $this->apiCalls[$key] = $json;

        return $json;
    }

    public function validatePostalCode(string $postalCode, string $alpha2Country) : bool
    {
        $codeUpper = mb_strtoupper($alpha2Country);

        $json = $this->makeApiCall(
            $postalCode,
            $codeUpper
        );

        $jsonCountry = $json['country abbreviation'] ?? null;

        return $jsonCountry === $codeUpper;
    }

    public function fillModelFromPostalCode(UserModel $model) : void
    {
        $postalCode = $model->getBillingPostalCode();

        $countryCode = $model->getBillingCountry();

        if (! $this->validatePostalCode($postalCode, $countryCode)) {
            return;
        }

        $json = $this->makeApiCall($postalCode, $countryCode);

        $place = $json['places'][0] ?? [];

        $model->setBillingCity($place['place name'] ?? '');

        $model->setBillingStateAbbr($place['state abbreviation'] ?? '');
    }
}
