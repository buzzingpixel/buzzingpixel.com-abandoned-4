<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Email\EmailApi;
use App\Email\Models\EmailModel;
use App\Payload\Payload;
use App\Users\Models\UserModel;
use Config\General;
use Throwable;
use Twig\Environment as TwigEnvironment;

class RequestPasswordResetEmail
{
    private GeneratePasswordResetToken $generatePasswordResetToken;
    private EmailApi $emailApi;
    private TwigEnvironment $twigEnvironment;
    private General $config;

    public function __construct(
        GeneratePasswordResetToken $generatePasswordResetToken,
        EmailApi $emailApi,
        TwigEnvironment $twigEnvironment,
        General $config
    ) {
        $this->generatePasswordResetToken = $generatePasswordResetToken;
        $this->emailApi                   = $emailApi;
        $this->twigEnvironment            = $twigEnvironment;
        $this->config                     = $config;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(UserModel $user) : void
    {
        $payload = ($this->generatePasswordResetToken)($user);

        if ($payload->getStatus() !== Payload::STATUS_CREATED) {
            return;
        }

        /** @var array<string, string> $result */
        $result = $payload->getResult();

        $emailModel            = new EmailModel();
        $emailModel->fromEmail = 'info@buzzingpixel.com';
        $emailModel->toEmail   = $user->emailAddress;
        $emailModel->subject   = 'Reset your password on BuzzingPixel.com';
        $emailModel->plainText = $this->twigEnvironment->render(
            'Email/PasswordResetEmail.twig',
            [
                'emailAddress' => $user->emailAddress,
                'link' => $this->config->siteUrl() .
                    '/account/reset-pw-with-token/' .
                    $result['id'],
            ]
        );

        $this->emailApi->queueEmail($emailModel);
    }
}
