<?php

declare(strict_types=1);

namespace App\Http\Admin\Analytics;

use App\Analytics\AnalyticsApi;
use App\Content\Meta\MetaPayload;
use DateTimeZone;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Safe\DateTimeImmutable;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetAnalyticsViewAction
{
    private TwigEnvironment $twig;
    private ResponseFactoryInterface $responseFactory;
    private AnalyticsApi $analyticsApi;

    public function __construct(
        TwigEnvironment $twig,
        ResponseFactoryInterface $responseFactory,
        AnalyticsApi $analyticsApi
    ) {
        $this->twig            = $twig;
        $this->responseFactory = $responseFactory;
        $this->analyticsApi    = $analyticsApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): ResponseInterface
    {
        $twentyFourHoursAgo = (new DateTimeImmutable())
            ->setTimezone(new DateTimeZone('UTC'))
            ->modify('-24 hours');

        $thirtyDaysAgo = (new DateTimeImmutable())
            ->setTimezone(new DateTimeZone('UTC'))
            ->modify('-30 days');

        $response = $this->responseFactory->createResponse(200);

        $response->getBody()->write($this->twig->render(
            'Http/Admin/AnalyticsView.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Analytics | Admin']
                ),
                'activeTab' => 'analytics',
                'stats' => [
                    'last24Hours' => [
                        'totalPageViews' => $this->analyticsApi
                            ->getTotalPageViewsSince($twentyFourHoursAgo),
                        'uniqueTotalVisitors' => $this->analyticsApi
                            ->getUniqueTotalVisitorsSince(
                                $twentyFourHoursAgo
                            ),
                        'uriStatsModels' => $this->analyticsApi
                            ->getUriStatsSince($twentyFourHoursAgo),
                    ],
                    'last30Days' => [
                        'totalPageViews' => $this->analyticsApi
                            ->getTotalPageViewsSince($thirtyDaysAgo),
                        'uniqueTotalVisitors' => $this->analyticsApi
                            ->getUniqueTotalVisitorsSince($thirtyDaysAgo),
                    ],
                ],
            ],
        ));

        return $response;
    }
}
