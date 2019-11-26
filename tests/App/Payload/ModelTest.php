<?php

declare(strict_types=1);

namespace Tests\App\Payload;

use App\Payload\Model;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function testInvalidProperty() : void
    {
        $exception = null;

        try {
            new class(['foo' => 'bar']) extends Model
            {
            };
        } catch (InvalidArgumentException $e) {
            $exception = $e;
        }

        self::assertInstanceOf(
            InvalidArgumentException::class,
            $exception
        );

        self::assertSame(
            'Property does not exist: foo',
            $exception->getMessage()
        );
    }
}
