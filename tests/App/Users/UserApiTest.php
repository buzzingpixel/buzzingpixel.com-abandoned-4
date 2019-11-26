<?php

declare(strict_types=1);

namespace Tests\App\Users;

use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\Services\FetchUserByEmailAddress;
use App\Users\Services\FetchUserById;
use App\Users\Services\SaveUser;
use App\Users\UserApi;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Throwable;
use function func_get_args;

class UserApiTest extends TestCase
{
    /** @var UserApi */
    private $userApi;

    /** @var Payload */
    private $payload;
    /** @var UserModel */
    private $userModel;

    /** @var mixed[] */
    private $callArgs = [];

    public function testSaveUser() : void
    {
        $payload = $this->userApi->saveUser($this->userModel);

        self::assertSame($this->payload, $payload);

        self::assertCount(1, $this->callArgs);

        self::assertSame(
            $this->userModel,
            $this->callArgs[0]
        );
    }

    public function testFetchUserByEmailAddress() : void
    {
        $userModel = $this->userApi->fetchUserByEmailAddress(
            'foo@bar.baz'
        );

        self::assertSame($this->userModel, $userModel);

        self::assertCount(1, $this->callArgs);

        self::assertSame(
            'foo@bar.baz',
            $this->callArgs[0]
        );
    }

    public function testFetchUserById() : void
    {
        $userModel = $this->userApi->fetchUserById('testId');

        self::assertSame($this->userModel, $userModel);

        self::assertCount(1, $this->callArgs);

        self::assertSame(
            'testId',
            $this->callArgs[0]
        );
    }

    protected function setUp() : void
    {
        $this->callArgs = [];

        $this->payload = new Payload(Payload::STATUS_SUCCESSFUL);

        $this->userModel = new UserModel();

        $this->userApi = new UserApi($this->mockDi());
    }

    /**
     * @return MockObject&ContainerInterface
     */
    private function mockDi() : ContainerInterface
    {
        $mock = $this->createMock(ContainerInterface::class);

        $mock->method('get')->willReturnCallback(
            [$this, 'containerGet']
        );

        return $mock;
    }

    /**
     * @return mixed
     *
     * @throws Throwable
     */
    public function containerGet(string $class)
    {
        switch ($class) {
            case SaveUser::class:
                return $this->mockSaveUser();
            case FetchUserByEmailAddress::class:
                return $this->mockFetchUserByEmailAddress();
            case FetchUserById::class:
                return $this->mockFetchUserById();
        }

        throw new Exception('Unknown class');
    }

    /**
     * @return SaveUser&MockObject
     */
    private function mockSaveUser() : SaveUser
    {
        $mock = $this->createMock(SaveUser::class);

        $mock->method('__invoke')
            ->willReturnCallback(function () {
                $this->callArgs = func_get_args();

                return $this->payload;
            });

        return $mock;
    }

    /**
     * @return FetchUserByEmailAddress&MockObject
     */
    private function mockFetchUserByEmailAddress() : FetchUserByEmailAddress
    {
        $mock = $this->createMock(
            FetchUserByEmailAddress::class
        );

        $mock->method('__invoke')
            ->willReturnCallback(function () {
                $this->callArgs = func_get_args();

                return $this->userModel;
            });

        return $mock;
    }

    /**
     * @return FetchUserById&MockObject
     */
    private function mockFetchUserById() : FetchUserById
    {
        $mock = $this->createMock(FetchUserById::class);

        $mock->method('__invoke')
            ->willReturnCallback(function () {
                $this->callArgs = func_get_args();

                return $this->userModel;
            });

        return $mock;
    }
}
