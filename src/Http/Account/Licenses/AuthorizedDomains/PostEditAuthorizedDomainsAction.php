<?php

declare(strict_types=1);

namespace App\Http\Account\Licenses\AuthorizedDomains;

use App\Licenses\LicenseApi;
use App\Payload\Payload;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Flash\Messages as FlashMessages;
use Throwable;

use function assert;
use function is_array;
use function Safe\preg_split;
use function trim;

class PostEditAuthorizedDomainsAction
{
    private LicenseApi $licenseApi;
    private FlashMessages $flashMessages;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        LicenseApi $licenseApi,
        FlashMessages $flashMessages,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->licenseApi      = $licenseApi;
        $this->flashMessages   = $flashMessages;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @throws Throwable
     * @throws HttpNotFoundException
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $id = (string) $request->getAttribute('id');

        $license = $this->licenseApi->fetchCurrentUserLicenseById($id);

        if ($license === null) {
            throw new HttpNotFoundException($request);
        }

        $post = $request->getParsedBody();

        assert(is_array($post));

        $domains = (string) ($post['authorized_domains'] ?? '');

        /** @var string[] $domainsArray */
        $domainsArray = preg_split('/\r\n|\r|\n/', $domains);

        $final = [];

        foreach ($domainsArray as $domain) {
            $domain = trim($domain);

            if ($domain === '') {
                continue;
            }

            $final[] = $domain;
        }

        $license->authorizedDomains = $final;

        $payload = $this->licenseApi->saveLicense($license);

        if ($payload->getStatus() !== Payload::STATUS_UPDATED) {
            $this->flashMessages->addMessage(
                'PostMessage',
                [
                    'status' => $payload->getStatus(),
                    'result' => ['message' => 'An unknown error occurred'],
                ]
            );

            return $this->responseFactory->createResponse(303)
                ->withHeader(
                    'Location',
                    '/account/licenses/authorized-domains/' . $id
                );
        }

        $this->flashMessages->addMessage(
            'PostMessage',
            [
                'status' => Payload::STATUS_SUCCESSFUL,
                'result' => ['message' => 'Authorized domains updated successfully'],
            ]
        );

        return $this->responseFactory->createResponse(303)
            ->withHeader(
                'Location',
                '/account/licenses/view/' . $id
            );
    }
}
