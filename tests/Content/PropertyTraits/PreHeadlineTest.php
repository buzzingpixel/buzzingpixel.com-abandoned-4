<?php

declare(strict_types=1);

namespace Tests\Content\PropertyTraits;

use App\Content\PropertyTraits\PreHeadline;
use PHPUnit\Framework\TestCase;

class PreHeadlineTest extends TestCase
{
    public function test(): void
    {
        $class = new class ('foo-pre-headline') {
            use PreHeadline;

            public function __construct(string $preHeadline)
            {
                $this->setPreHeadline($preHeadline);
            }
        };

        self::assertSame(
            'foo-pre-headline',
            $class->getPreHeadline(),
        );
    }
}
