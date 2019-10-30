<?php

declare(strict_types=1);

namespace Tests\App\HttpRespose\Twig\Extensions;

use App\HttpRespose\Twig\Extensions\RequireVariables;
use LogicException;
use PHPUnit\Framework\TestCase;

class RequireVariablesTest extends TestCase
{
    /** @var RequireVariables */
    private $requireVariables;

    protected function setUp() : void
    {
        $this->requireVariables = new RequireVariables();
    }

    public function testGetFunctions() : void
    {
        $return = $this->requireVariables->getFunctions();

        $twigFunc = $return[0];

        self::assertSame('requireVariables', $twigFunc->getName());

        /** @var array $callable */
        $callable = $twigFunc->getCallable();

        self::assertSame($this->requireVariables, $callable[0]);

        self::assertSame('requireVars', $callable[1]);

        self::assertFalse($twigFunc->needsEnvironment());

        self::assertTrue($twigFunc->needsContext());
    }

    public function testWhenVarDoesNotExist() : void
    {
        self::expectException(LogicException::class);

        self::expectExceptionMessage(
            'Variable "randomInstance" is required and must be of type "' . RandomInstance::class . '"'
        );

        $this->requireVariables->requireVars([], ['randomInstance' => RandomInstance::class]);
    }

    public function testWhenTypeIsNullAndVarExists() : void
    {
        $this->requireVariables->requireVars(
            ['randomInstance' => 'foo'],
            ['randomInstance' => null]
        );

        self::assertTrue(true);
    }

    public function testWhenVarTypeIsClassAndNotInstance() : void
    {
        self::expectException(LogicException::class);

        self::expectExceptionMessage(
            'Variable "randomInstance" is required and must be of type "' . RandomInstance::class . '"'
        );

        $this->requireVariables->requireVars(
            ['randomInstance' => 'foo'],
            ['randomInstance' => RandomInstance::class]
        );
    }

    public function testWhenVarTypeIsClassAndIsInstance() : void
    {
        $this->requireVariables->requireVars(
            ['randomInstance' => new RandomInstance()],
            ['randomInstance' => RandomInstance::class]
        );

        self::assertTrue(true);
    }

    public function testWhenVarTypeIsStringAndInstanceNotString() : void
    {
        self::expectException(LogicException::class);

        self::expectExceptionMessage(
            'Variable "randomInstance" is required and must be of type "string"'
        );

        $this->requireVariables->requireVars(
            ['randomInstance' => 23],
            ['randomInstance' => 'string']
        );
    }

    public function testWhenVarTypeIsStringAndInstanceIsString() : void
    {
        $this->requireVariables->requireVars(
            ['randomInstance' => 'Foo'],
            ['randomInstance' => 'string']
        );

        self::assertTrue(true);
    }

    public function testWhenVarTypeIsIntegerAndInstanceNotInteger() : void
    {
        self::expectException(LogicException::class);

        self::expectExceptionMessage(
            'Variable "randomInstance" is required and must be of type "integer"'
        );

        $this->requireVariables->requireVars(
            ['randomInstance' => '23'],
            ['randomInstance' => 'integer']
        );
    }

    public function testWhenVarTypeIsIntegerAndInstanceIsInteger() : void
    {
        $this->requireVariables->requireVars(
            ['randomInstance' => 123],
            ['randomInstance' => 'integer']
        );

        self::assertTrue(true);
    }
}
