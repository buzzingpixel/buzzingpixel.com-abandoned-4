<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Software;

use App\Http\Admin\Software\PostAdminSoftwareEditAction;
use App\Http\Admin\Software\PostAdminSoftwareEditResponder;
use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use App\Software\SoftwareApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Throwable;
use function assert;

class PostAdminSoftwareEditActionTest extends TestCase
{
    public function testWhenNoSoftware() : void
    {
        $responder = $this->createMock(
            PostAdminSoftwareEditResponder::class
        );

        $responder->expects(self::never())
            ->method(self::anything());

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareById')
            ->with(self::equalTo('foo-id'))
            ->willReturn(null);

        $action = new PostAdminSoftwareEditAction(
            $responder,
            $softwareApi
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([]);

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $exception = null;

        try {
            $action($request);
        } catch (HttpBadRequestException $e) {
            $exception = $e;
        }

        assert($exception instanceof HttpBadRequestException);

        self::assertSame(
            $request,
            $exception->getRequest(),
        );

        self::assertSame(
            'Software for specified ID foo-id could not be found',
            $exception->getMessage()
        );
    }

    /**
     * @throws Throwable
     */
    public function testPostErrors() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostAdminSoftwareEditResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $payload,
                    string $id
                ) use (
                    $response
                ) {
                    self::assertSame(
                        Payload::STATUS_NOT_VALID,
                        $payload->getStatus()
                    );

                    self::assertSame(
                        $payload->getResult(),
                        [
                            'message' => 'There were errors with your submission',
                            'inputMessages' => [
                                'name' => 'Name is required',
                                'slug' => 'Slug is required',
                                'price' => 'Price must be specified as integer or float',
                                'renewal_price' => 'Renewal Price must be specified as integer or float',
                            ],
                            'inputValues' => [
                                'name' => '',
                                'slug' => '',
                                'for_sale' => false,
                                'price' => '',
                                'renewal_price' => '',
                                'subscription' => false,
                            ],
                        ],
                    );

                    self::assertSame('foo-id', $id);

                    return $response;
                }
            );

        $software = new SoftwareModel();

        $software->slug = 'orig-slug';

        $software->id = 'foo-id';

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($software);

        $softwareApi->expects(self::never())
            ->method('saveSoftware');

        $action = new PostAdminSoftwareEditAction(
            $responder,
            $softwareApi
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([]);

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $returnResponse = $action($request);

        self::assertSame($response, $returnResponse);
    }

    /**
     * @throws Throwable
     */
    public function testWhenApiPayloadNotUpdated() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostAdminSoftwareEditResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $payload,
                    string $id
                ) use (
                    $response
                ) {
                    self::assertSame(
                        Payload::STATUS_NOT_UPDATED,
                        $payload->getStatus()
                    );

                    self::assertSame(
                        $payload->getResult(),
                        ['message' => 'An unknown error occurred'],
                    );

                    self::assertSame('bar-id', $id);

                    return $response;
                }
            );

        $software = new SoftwareModel();

        $software->slug = 'orig-slug';

        $software->id = 'bar-id';

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($software);

        $softwareApi->expects(self::once())
            ->method('saveSoftware')
            ->with(self::equalTo($software))
            ->willReturn(new Payload(Payload::STATUS_ERROR));

        $action = new PostAdminSoftwareEditAction(
            $responder,
            $softwareApi
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'name' => 'foo-name',
                'slug' => 'foo-id',
                'for_sale' => 'true',
                'price' => '987.65',
                'renewal_price' => '293.84',
                'subscription' => 'true',
            ]);

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $returnResponse = $action($request);

        self::assertSame($response, $returnResponse);

        self::assertSame(
            'foo-name',
            $software->name,
        );

        self::assertSame(
            'foo-id',
            $software->slug,
        );

        self::assertTrue($software->isForSale);

        self::assertSame(
            987.65,
            $software->price,
        );

        self::assertSame(
            293.84,
            $software->renewalPrice,
        );

        self::assertTrue($software->isSubscription);
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $payload = new Payload(Payload::STATUS_UPDATED);

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostAdminSoftwareEditResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $incomingPayload,
                    string $origSlug
                ) use (
                    $response,
                    $payload
                ) {
                    self::assertSame(
                        $payload,
                        $incomingPayload
                    );

                    self::assertSame('foo-id', $origSlug);

                    return $response;
                }
            );

        $software = new SoftwareModel();

        $software->slug = 'orig-slug';

        $software->id = 'foo-id';

        $softwareApi = $this->createMock(SoftwareApi::class);

        $softwareApi->expects(self::once())
            ->method('fetchSoftwareById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($software);

        $softwareApi->expects(self::once())
            ->method('saveSoftware')
            ->with(self::equalTo($software))
            ->willReturn($payload);

        $action = new PostAdminSoftwareEditAction(
            $responder,
            $softwareApi
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'name' => 'foo-name',
                'slug' => 'foo-id',
                'for_sale' => 'true',
                'price' => '987.65',
                'renewal_price' => '293.84',
                'subscription' => 'true',
            ]);

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $returnResponse = $action($request);

        self::assertSame($response, $returnResponse);

        self::assertSame(
            'foo-name',
            $software->name,
        );

        self::assertSame(
            'foo-id',
            $software->slug,
        );

        self::assertTrue($software->isForSale);

        self::assertSame(
            987.65,
            $software->price,
        );

        self::assertSame(
            293.84,
            $software->renewalPrice,
        );

        self::assertTrue($software->isSubscription);
    }
}
