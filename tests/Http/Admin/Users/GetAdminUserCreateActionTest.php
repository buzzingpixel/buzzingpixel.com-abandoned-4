<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Users;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use App\Http\Admin\Users\GetAdminUserCreateAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use function assert;

class GetAdminUserCreateActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
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
                    $response
                ) {
                    self::assertSame(
                        'Admin/UserCreate.twig',
                        $template,
                    );

                    self::assertCount(3, $context);

                    $metaPayload = $context['metaPayload'];

                    assert($metaPayload instanceof MetaPayload);

                    self::assertSame(
                        'Create New User | Admin',
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
                            ['content' => 'Create New User'],
                        ],
                        $context['breadcrumbs'],
                    );

                    return $response;
                }
            );

        $action = new GetAdminUserCreateAction($responder);

        self::assertSame($response, $action());
    }
}
