<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Users;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use App\Http\Admin\Users\GetAdminSearchUsersDisplayAction;
use App\HttpHelpers\Pagination\Pagination;
use App\HttpHelpers\Segments\ExtractUriSegments;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\Exception\HttpNotFoundException;
use Tests\TestConfig;
use Throwable;
use function assert;

class GetAdminSearchUsersDisplayActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testWhenNoQuery() : void
    {
        $userApi = $this->createMock(UserApi::class);

        $responder = $this->createMock(
            GetAdminResponder::class
        );

        $responder->expects(self::never())
            ->method(self::anything());

        $action = new GetAdminSearchUsersDisplayAction(
            TestConfig::$di->get(ExtractUriSegments::class),
            $userApi,
            $responder,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

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
        $response = $this->createMock(
            ResponseInterface::class
        );

        $users = [
            new UserModel(),
            new UserModel(),
        ];

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchUsersBySearch')
            ->with(
                self::equalTo('%foo-query%'),
                self::equalTo(50),
                self::equalTo(100),
            )
            ->willReturn($users);

        $userApi->expects(self::once())
            ->method('fetchTotalUsers')
            ->willReturn(45678);

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
                    $users
                ) {
                    self::assertSame(
                        'Admin/Users.twig',
                        $template,
                    );

                    self::assertCount(5, $context);

                    $metaPayload = $context['metaPayload'];

                    assert($metaPayload instanceof MetaPayload);

                    self::assertSame(
                        'User Search | Admin',
                        $metaPayload->getMetaTitle(),
                    );

                    self::assertSame(
                        'users',
                        $context['activeTab'],
                    );

                    self::assertSame(
                        $users,
                        $context['userModels'],
                    );

                    self::assertSame(
                        'foo-query',
                        $context['searchTerm'],
                    );

                    $pagination = $context['pagination'];

                    assert($pagination instanceof Pagination);

                    self::assertSame(
                        '/foo/bar',
                        $pagination->base(),
                    );

                    self::assertSame(
                        3,
                        $pagination->currentPage()
                    );

                    self::assertSame(
                        50,
                        $pagination->perPage()
                    );

                    self::assertSame(
                        45678,
                        $pagination->totalResults(),
                    );

                    return $response;
                }
            );

        $action = new GetAdminSearchUsersDisplayAction(
            TestConfig::$di->get(ExtractUriSegments::class),
            $userApi,
            $responder,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $uri = $this->createMock(UriInterface::class);

        $uri->expects(self::once())
            ->method('getPath')
            ->willReturn('/foo/bar/page/3');

        $request->expects(self::once())
            ->method('getUri')
            ->willReturn($uri);

        $request->expects(self::once())
            ->method('getQueryParams')
            ->willReturn(['q' => 'foo-query']);

        $returnResponse = $action($request);

        self::assertSame($response, $returnResponse);
    }
}
