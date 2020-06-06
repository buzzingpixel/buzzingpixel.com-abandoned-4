<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Users;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use App\Http\Admin\Users\GetAdminUserViewAction;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;
use function assert;

class GetAdminUserViewActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testWhenNoUser() : void
    {
        $responder = $this->createMock(
            GetAdminResponder::class
        );

        $responder->expects(self::never())
            ->method(self::anything());

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchUserById')
            ->with(self::equalTo('foo-id'))
            ->willReturn(null);

        $action = new GetAdminUserViewAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $exception = null;

        try {
            $action($request);
        } catch (HttpNotFoundException $e) {
            $exception = $e;
        }

        assert($exception instanceof HttpNotFoundException);

        self::assertSame($request, $exception->getRequest());
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $user = new UserModel();

        $user->emailAddress = 'foo@bar.baz';

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            GetAdminResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    string $template,
                    array $context
                ) use (
                    $response,
                    $user
                ) {
                    self::assertSame(
                        'Http/Admin/UserView.twig',
                        $template,
                    );

                    self::assertCount(4, $context);

                    $metaPayload = $context['metaPayload'];

                    assert($metaPayload instanceof MetaPayload);

                    self::assertSame(
                        'foo@bar.baz | Admin',
                        $metaPayload->getMetaTitle(),
                    );

                    self::assertSame(
                        'users',
                        $context['activeTab'],
                    );

                    self::assertSame(
                        [
                            [
                                'href' => '/admin/users',
                                'content' => 'Users Admin',
                            ],
                        ],
                        $context['breadcrumbs'],
                    );

                    self::assertSame(
                        $context['user'],
                        $user,
                    );

                    return $response;
                }
            );

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchUserById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($user);

        $action = new GetAdminUserViewAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        self::assertSame($response, $action($request));
    }
}
