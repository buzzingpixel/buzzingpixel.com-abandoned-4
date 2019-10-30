<?php

declare(strict_types=1);

namespace Tests\App\Payload;

use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use Throwable;

class SpecificPayloadTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testDoubleInit() : void
    {
        $objectToTest = new SpecificPayloadImplementation();

        self::expectException(LogicException::class);

        self::expectExceptionMessage(
            SpecificPayloadImplementation::class . ' instances can only be initialized once.'
        );

        $objectToTest->__construct();
    }

    /**
     * @throws Throwable
     */
    public function testSetInvalidProperty() : void
    {
        self::expectException(InvalidArgumentException::class);

        self::expectExceptionMessage('Property does not exist: FooBar');

        new SpecificPayloadImplementation(['FooBar' => 'var']);
    }

    /**
     * @throws Throwable
     */
    public function testSetProperty() : void
    {
        $objectToTest = new SpecificPayloadImplementation(['Bar' => 'TestVal']);

        self::assertSame('TestVal', $objectToTest->getBar());
    }

    public function testNoProperties() : void
    {
        $objectToTest = new SpecificPayloadImplementation();

        self::assertNull($objectToTest->getBar());
    }

    public function testGetShortname() : void
    {
        $objectToTest = new SpecificPayloadImplementation();

        self::assertSame(
            'SpecificPayloadImplementation',
            $objectToTest->getShortName()
        );
    }
}
