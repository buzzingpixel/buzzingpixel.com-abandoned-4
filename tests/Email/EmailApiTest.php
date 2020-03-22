<?php

declare(strict_types=1);

namespace Tests\Email;

use App\Email\EmailApi;
use App\Email\Interfaces\SendMailAdapter;
use App\Email\Models\EmailModel;
use App\Payload\Payload;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class EmailApiTest extends TestCase
{
    public function test() : void
    {
        $payload = new Payload(Payload::STATUS_SUCCESSFUL);

        $email = new EmailModel();

        $sendMail = $this->createMock(SendMailAdapter::class);

        $sendMail->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($email))
            ->willReturn($payload);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(SendMailAdapter::class)
            ->willReturn($sendMail);

        $api = new EmailApi($di);

        self::assertSame(
            $payload,
            $api->sendMail($email)
        );
    }
}
