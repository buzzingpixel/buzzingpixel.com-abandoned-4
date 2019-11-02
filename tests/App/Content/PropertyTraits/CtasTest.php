<?php

declare(strict_types=1);

namespace Tests\App\Content\PropertyTraits;

use App\Content\Modules\Payloads\CtaPayload;
use App\Content\PropertyTraits\Ctas;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CtasTest extends TestCase
{
    public function testInvalidInstance() : void
    {
        self::expectException(InvalidArgumentException::class);

        self::expectExceptionMessage(
            'Cta must be instance of ' . CtaPayload::class
        );

        $trait = new class()
        {
            use Ctas;

            /**
             * @param mixed[] $ctas
             */
            public function runTest(array $ctas) : void
            {
                $this->setCtas($ctas);
            }
        };

        $trait->runTest(['foobar']);
    }
}
