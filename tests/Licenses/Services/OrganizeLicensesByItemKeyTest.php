<?php

declare(strict_types=1);

namespace Tests\Licenses\Services;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Services\OrganizeLicensesByItemKey;
use PHPUnit\Framework\TestCase;
use Throwable;

class OrganizeLicensesByItemKeyTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $license1          = new LicenseModel();
        $license1->itemKey = 'yolo';

        $license2          = new LicenseModel();
        $license2->itemKey = 'asap';

        $license3          = new LicenseModel();
        $license3->itemKey = 'yolo';

        $license4          = new LicenseModel();
        $license4->itemKey = 'asap';

        $service = new OrganizeLicensesByItemKey();

        $returnItems = $service([
            $license1,
            $license2,
            $license3,
            $license4,
        ]);

        self::assertCount(2, $returnItems);

        self::assertSame(
            [
                $license2,
                $license4,
            ],
            $returnItems['asap'],
        );

        self::assertSame(
            [
                $license1,
                $license3,
            ],
            $returnItems['yolo'],
        );
    }
}
