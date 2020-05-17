<?php

declare(strict_types=1);

namespace App\Http\Admin\Software;

use App\Payload\Payload;
use App\Software\SoftwareApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use function assert;
use function count;
use function is_array;
use function is_numeric;

class PostAdminSoftwareEditAction
{
    private PostAdminSoftwareEditResponder $responder;
    private SoftwareApi $softwareApi;

    public function __construct(
        PostAdminSoftwareEditResponder $responder,
        SoftwareApi $softwareApi
    ) {
        $this->responder   = $responder;
        $this->softwareApi = $softwareApi;
    }

    /**
     * @throws HttpBadRequestException
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $postData = $request->getParsedBody();

        assert(is_array($postData));

        $inputValues = [
            'name' => $postData['name'] ?? '',
            'slug' => $postData['slug'] ?? '',
            'for_sale' => ($postData['for_sale'] ?? '') === 'true',
            'price' => $postData['price'] ?? '',
            'renewal_price' => $postData['renewal_price'] ?? '',
            'subscription' => ($postData['subscription'] ?? '') === 'true',
        ];

        $idAttribute = (string) $request->getAttribute('id');

        $software = $this->softwareApi->fetchSoftwareById(
            $idAttribute
        );

        if ($software === null) {
            throw new HttpBadRequestException(
                $request,
                'Software for specified ID ' .
                    $idAttribute .
                    ' could not be found',
            );
        }

        $originalSlug = $software->slug;

        $inputMessages = [];

        if ($inputValues['name'] === '') {
            $inputMessages['name'] = 'Name is required';
        }

        if ($inputValues['slug'] === '') {
            $inputMessages['slug'] = 'Slug is required';
        }

        if (! is_numeric($inputValues['price'])) {
            $inputMessages['price'] = 'Price must be specified as integer or float';
        }

        if (! is_numeric($inputValues['renewal_price'])) {
            $inputMessages['renewal_price'] = 'Renewal Price must be specified as integer or float';
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
                $originalSlug,
            );
        }

        $software->name           = (string) $inputValues['name'];
        $software->slug           = (string) $inputValues['slug'];
        $software->isForSale      = $inputValues['for_sale'];
        $software->price          = (float) $inputValues['price'];
        $software->renewalPrice   = (float) $inputValues['renewal_price'];
        $software->isSubscription = $inputValues['subscription'];

        $payload = $this->softwareApi->saveSoftware($software);

        if ($payload->getStatus() !== Payload::STATUS_UPDATED) {
            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_UPDATED,
                    ['message' => 'An unknown error occurred'],
                ),
                $originalSlug,
            );
        }

        return ($this->responder)(
            $payload,
            $software->slug
        );
    }
}
