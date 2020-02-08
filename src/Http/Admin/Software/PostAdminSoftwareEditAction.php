<?php

declare(strict_types=1);

namespace App\Http\Admin\Software;

use App\Payload\Payload;
use App\Software\SoftwareApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use function count;
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

        $inputValues = [
            'name' => $postData['name'] ?? '',
            'slug' => $postData['slug'] ?? '',
            'for_sale' => ($postData['for_sale'] ?? '') === 'true',
            'price' => $postData['price'] ?? '',
            'renewal_price' => $postData['renewal_price'] ?? '',
            'subscription' => ($postData['subscription'] ?? '') === 'true',
        ];

        $softwareModel = $this->softwareApi->fetchSoftwareBySlug(
            (string) $request->getAttribute('slug')
        );

        if ($softwareModel === null) {
            throw new HttpBadRequestException(
                $request,
                'Software for specified Slug ' .
                    (string) $request->getAttribute('slug') .
                    ' could not be found',
            );
        }

        $originalSlug = $softwareModel->getSlug();

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

        $softwareModel->setName((string) $inputValues['name']);
        $softwareModel->setSlug((string) $inputValues['slug']);
        $softwareModel->setIsForSale($inputValues['for_sale']);
        $softwareModel->setPrice((float) $inputValues['price']);
        $softwareModel->setRenewalPrice(
            (float) $inputValues['renewal_price']
        );
        $softwareModel->setIsSubscription(
            $inputValues['subscription']
        );

        $payload = $this->softwareApi->saveSoftware($softwareModel);

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
            $softwareModel->getSlug()
        );
    }
}
