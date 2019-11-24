<?php

declare(strict_types=1);

namespace Tests\App\CliServices;

use App\CliServices\CliQuestionService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use function func_get_args;

class CliQuestionServiceTest extends TestCase
{
    /** @var CliQuestionService */
    private $cliQuestionService;

    /** @var MockObject&QuestionHelper */
    private $questionHelper;
    /** @var MockObject&InputInterface */
    private $consoleInput;
    /** @var MockObject&OutputInterface */
    private $consoleOutput;

    /** @var int */
    private $questionHelperReturnsOn = 2;
    /** @var string */
    private $questionHelperReturnValue = 'FooBar';

    public function testDefault() : void
    {
        $this->questionHelperReturnsOn   = 2;
        $this->questionHelperReturnValue = 'FooBar';

        $val = $this->cliQuestionService->ask('Test Question');

        self::assertSame('FooBar', $val);

        self::assertCount(2, $this->questionHelperCallArgs);

        $args1 = $this->questionHelperCallArgs[0];
        self::assertCount(3, $args1);
        self::assertSame($this->consoleInput, $args1[0]);
        self::assertSame($this->consoleOutput, $args1[1]);
        /** @var Question|null $args1Question */
        $args1Question = $args1[2];
        self::assertInstanceOf(Question::class, $args1Question);
        self::assertSame('Test Question', $args1Question->getQuestion());
        self::assertFalse($args1Question->isHidden());

        $args2 = $this->questionHelperCallArgs[1];
        self::assertCount(3, $args2);
        self::assertSame($this->consoleInput, $args2[0]);
        self::assertSame($this->consoleOutput, $args2[1]);
        /** @var Question|null $args2Question */
        $args2Question = $args2[2];
        self::assertInstanceOf(Question::class, $args2Question);
        self::assertSame('Test Question', $args2Question->getQuestion());
        self::assertFalse($args2Question->isHidden());
    }

    public function testIsHiddenNotRequired() : void
    {
        $this->questionHelperReturnsOn   = 2;
        $this->questionHelperReturnValue = 'FooBar';

        $val = $this->cliQuestionService->ask('Foo Question', false, true);

        self::assertSame('', $val);

        self::assertCount(1, $this->questionHelperCallArgs);

        $args1 = $this->questionHelperCallArgs[0];
        self::assertCount(3, $args1);
        self::assertSame($this->consoleInput, $args1[0]);
        self::assertSame($this->consoleOutput, $args1[1]);
        /** @var Question|null $args1Question */
        $args1Question = $args1[2];
        self::assertInstanceOf(Question::class, $args1Question);
        self::assertSame('Foo Question', $args1Question->getQuestion());
        self::assertTrue($args1Question->isHidden());
    }

    /** @var int */
    private $questionHelperCalls = 0;
    /** @var mixed[] */
    private $questionHelperCallArgs = [];

    protected function setUp() : void
    {
        $this->questionHelperCalls = 0;

        $this->questionHelper = $this->createMock(
            QuestionHelper::class
        );

        $this->questionHelper->method('ask')
            ->willReturnCallback(function () {
                $this->questionHelperCalls += 1;

                $this->questionHelperCallArgs[] = func_get_args();

                if ($this->questionHelperCalls === $this->questionHelperReturnsOn) {
                    return $this->questionHelperReturnValue;
                }

                return '';
            });

        $this->consoleInput = $this->createMock(
            InputInterface::class
        );

        $this->consoleOutput = $this->createMock(
            OutputInterface::class
        );

        $this->cliQuestionService = new CliQuestionService(
            $this->questionHelper,
            $this->consoleInput,
            $this->consoleOutput
        );
    }
}
