<?php

declare(strict_types=1);

namespace App\Analytics;

use App\Analytics\Models\AnalyticsModel;
use App\Analytics\Services\CreatePageView;
use App\Analytics\Services\GetTotalPageViewsSince;
use App\Payload\Payload;
use Psr\Container\ContainerInterface;

use function assert;

class AnalyticsApi
{
    private ContainerInterface $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function createPageView(AnalyticsModel $model): Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(CreatePageView::class);

        assert($service instanceof CreatePageView);

        return $service($model);
    }

    public function getTotalPageViewsSince(?DateTimeImmutable $date = null): int
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(GetTotalPageViewsSince::class);

        assert($service instanceof GetTotalPageViewsSince);

        return $service($date);
    }
}
