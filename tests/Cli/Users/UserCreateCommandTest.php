<?php

declare(strict_types=1);

namespace Tests\Cli\Users;

use App\Cli\Users\UserCreateCommand;
use App\CliServices\CliQuestionService;
use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function func_get_args;

class UserCreateCommandTest extends TestCase
{
    /** @var UserCreateCommand */
    private $command;

    /** @var Payload|null */
    private $payload;

    /** @var mixed[] */
    private $callArgs;

    public function testWhenPayloadStatusIsNotCreated() : void
    {
        $this->payload = new Payload(Payload::STATUS_NOT_CREATED, [
            'foo' => 'bar',
            'baz' => 'foo',
        ]);

        $this->internalSetup();

        $output = $this->createMock(OutputInterface::class);

        $output->expects(self::at(0))
            ->method('writeln')
            ->with(
                self::equalTo('<fg=red>An error occurred</>')
            );

        $output->expects(self::at(1))
            ->method('writeln')
            ->with(
                self::equalTo('<fg=red>foo: bar</>')
            );

        $output->expects(self::at(2))
            ->method('writeln')
            ->with(
                self::equalTo('<fg=red>baz: foo</>')
            );

        $return = $this->command->execute(
            $this->createMock(InputInterface::class),
            $output
        );

        self::assertSame(1, $return);

        self::assertCount(1, $this->callArgs);

        /** @var UserModel|null $userModel */
        $userModel = $this->callArgs[0];

        self::assertInstanceOf(UserModel::class, $userModel);

        self::assertSame('foo@bar.baz', $userModel->getEmailAddress());

        self::assertSame('TestPass', $userModel->getNewPassword());
    }

    public function testWhenPayloadStatusIsCreated() : void
    {
        $this->payload = new Payload(Payload::STATUS_CREATED, ['message' => 'FooBar']);

        $this->internalSetup();

        $output = $this->createMock(OutputInterface::class);

        $output->expects(self::once())
            ->method('writeln')
            ->with(
                self::equalTo('<fg=green>FooBar</>')
            );

        $return = $this->command->execute(
            $this->createMock(InputInterface::class),
            $output
        );

        self::assertSame(0, $return);

        self::assertCount(1, $this->callArgs);

        /** @var UserModel|null $userModel */
        $userModel = $this->callArgs[0];

        self::assertInstanceOf(UserModel::class, $userModel);

        self::assertSame('foo@bar.baz', $userModel->getEmailAddress());

        self::assertSame('TestPass', $userModel->getNewPassword());
    }

    private function internalSetup() : void
    {
        $this->callArgs = [];

        $this->command = new UserCreateCommand(
            $this->mockQuestionService(),
            $this->mockUserApi()
        );

        self::assertSame('user:create', $this->command->getName());
    }

    /**
     * @return CliQuestionService&MockObject
     */
    private function mockQuestionService() : CliQuestionService
    {
        $mock = $this->createMock(CliQuestionService::class);

        $mock->expects(self::at(0))
            ->method('ask')
            ->with(
                self::equalTo('<fg=cyan>Email address: </>')
            )
            ->willReturn('foo@bar.baz');

        $mock->expects(self::at(1))
            ->method('ask')
            ->with(
                self::equalTo('<fg=cyan>Password: </>')
            )
            ->willReturn('TestPass');

        return $mock;
    }

    /**
     * @return UserApi&MockObject
     */
    private function mockUserApi() : UserApi
    {
        $mock = $this->createMock(UserApi::class);

        $mock->method('saveUser')
            ->willReturnCallback(function () {
                $this->callArgs = func_get_args();

                return $this->payload;
            });

        return $mock;
    }
}
