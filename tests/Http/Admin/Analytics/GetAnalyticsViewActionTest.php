<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Analytics;

use App\Analytics\AnalyticsApi;
use App\Analytics\Models\UriStatsModel;
use App\Content\Meta\MetaPayload;
use App\Http\Admin\Analytics\GetAnalyticsViewAction;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Safe\DateTimeImmutable;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;

use function assert;

class GetAnalyticsViewActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $uriStatsModel = new UriStatsModel();

        $twentyFourHoursAgo = (new DateTimeImmutable())
            ->setTimezone(new DateTimeZone('UTC'))
            ->modify('-24 hours');

        $thirtyDaysAgo = (new DateTimeImmutable())
            ->setTimezone(new DateTimeZone('UTC'))
            ->modify('-30 days');

        $analyticsApi = $this->createMock(
            AnalyticsApi::class
        );

        $analyticsApi->expects(self::at(0))
            ->method('getTotalPageViewsSince')
            ->willReturnCallback(
                static function (DateTimeImmutable $date) use (
                    $twentyFourHoursAgo
                ): int {
                    self::assertSame(
                        $twentyFourHoursAgo->format('Y-m-d H:i'),
                        $date->format('Y-m-d H:i'),
                    );

                    return 325;
                }
            );

        $analyticsApi->expects(self::at(1))
            ->method('getUniqueTotalVisitorsSince')
            ->willReturnCallback(
                static function (DateTimeImmutable $date) use (
                    $twentyFourHoursAgo
                ): int {
                    self::assertSame(
                        $twentyFourHoursAgo->format('Y-m-d H:i'),
                        $date->format('Y-m-d H:i'),
                    );

                    return 476;
                }
            );

        $analyticsApi->expects(self::at(2))
            ->method('getUriStatsSince')
            ->willReturnCallback(
                static function (DateTimeImmutable $date) use (
                    $twentyFourHoursAgo,
                    $uriStatsModel
                ): array {
                    self::assertSame(
                        $twentyFourHoursAgo->format('Y-m-d H:i'),
                        $date->format('Y-m-d H:i'),
                    );

                    return [$uriStatsModel];
                }
            );

        $analyticsApi->expects(self::at(3))
            ->method('getTotalPageViewsSince')
            ->willReturnCallback(
                static function (DateTimeImmutable $date) use (
                    $thirtyDaysAgo
                ): int {
                    self::assertSame(
                        $thirtyDaysAgo->format('Y-m-d H:i'),
                        $date->format('Y-m-d H:i'),
                    );

                    return 2345;
                }
            );

        $analyticsApi->expects(self::at(4))
            ->method('getUniqueTotalVisitorsSince')
            ->willReturnCallback(
                static function (DateTimeImmutable $date) use (
                    $thirtyDaysAgo
                ): int {
                    self::assertSame(
                        $thirtyDaysAgo->format('Y-m-d H:i'),
                        $date->format('Y-m-d H:i'),
                    );

                    return 35433;
                }
            );

        $twig = $this->createMock(TwigEnvironment::class);

        $twig->expects(self::once())
            ->method('render')
            ->willReturnCallback(
                static function (
                    string $template,
                    array $context
                ) use ($uriStatsModel): string {
                    self::assertSame(
                        'Http/Admin/AnalyticsView.twig',
                        $template,
                    );

                    self::assertCount(3, $context);

                    $metaPayload = $context['metaPayload'];
                    assert($metaPayload instanceof MetaPayload);

                    self::assertSame(
                        'Analytics | Admin',
                        $metaPayload->getMetaTitle(),
                    );

                    self::assertSame(
                        'analytics',
                        $context['activeTab'],
                    );

                    $stats = $context['stats'];

                    self::assertCount(2, $stats);

                    $last24Hours = $stats['last24Hours'];

                    self::assertCount(3, $last24Hours);

                    self::assertSame(
                        325,
                        $last24Hours['totalPageViews'],
                    );

                    self::assertSame(
                        476,
                        $last24Hours['uniqueTotalVisitors'],
                    );

                    self::assertSame(
                        [$uriStatsModel],
                        $last24Hours['uriStatsModels'],
                    );

                    $last30Days = $stats['last30Days'];

                    self::assertCount(2, $last30Days);

                    self::assertSame(
                        2345,
                        $last30Days['totalPageViews'],
                    );

                    self::assertSame(
                        35433,
                        $last30Days['uniqueTotalVisitors'],
                    );

                    return 'foo-twig-return';
                }
            );

        $action = new GetAnalyticsViewAction(
            $twig,
            TestConfig::$di->get(
                ResponseFactoryInterface::class,
            ),
            $analyticsApi,
        );

        $response = $action();

        self::assertSame(200, $response->getStatusCode());

        self::assertSame(
            'foo-twig-return',
            $response->getBody()->__toString()
        );
    }
}
