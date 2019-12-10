<?php

declare(strict_types=1);

namespace Tests\Http\Account\LogIn;

use App\Http\Account\LogIn\PostLogInResponder;
use App\Payload\Payload;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Slim\Flash\Messages as FlashMessages;
use Slim\Psr7\Factory\ResponseFactory;

class PostLogInResponderTest extends TestCase
{
    /** @var PostLogInResponder */
    private $responder;

    /** @var Payload */
    private $payload;

    public function testWhenNotSuccessful() : void
    {
        $this->internalSetUp(Payload::STATUS_ERROR);

        $response = ($this->responder)(
            $this->payload,
            '/foo/redirect'
        );

        self::assertSame(303, $response->getStatusCode());

        $headers = $response->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Location']);

        self::assertSame($headers['Location'][0], '/foo/redirect');
    }

    public function testWhenSuccessful() : void
    {
        $this->internalSetUp();

        $response = ($this->responder)(
            $this->payload,
            '/bar/redirect'
        );

        self::assertSame(303, $response->getStatusCode());

        $headers = $response->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Location']);

        self::assertSame($headers['Location'][0], '/bar/redirect');
    }

    private function internalSetUp(
        string $payloadStatus = Payload::STATUS_SUCCESSFUL
    ) : void {
        $this->payload = new Payload(
            $payloadStatus,
            ['foo' => 'bar']
        );

        $this->responder = new PostLogInResponder(
            $this->mockFlashMessages($this->payload),
            new ResponseFactory()
        );
    }

    /**
     * @return MockObject&FlashMessages
     */
    private function mockFlashMessages(Payload $payload) : FlashMessages
    {
        $mock = $this->createMock(FlashMessages::class);

        if ($payload->getStatus() === Payload::STATUS_SUCCESSFUL) {
            $mock->expects(self::never())
                ->method(self::anything());
        } else {
            $mock->expects(self::once())
                ->method('addMessage')
                ->with(
                    self::equalTo('LoginFormMessage'),
                    [
                        'status' => $payload->getStatus(),
                        'result' => $payload->getResult(),
                    ]
                );
        }

        return $mock;
    }
}
