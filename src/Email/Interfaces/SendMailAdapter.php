<?php

declare(strict_types=1);

namespace App\Email\Interfaces;

use App\Email\Models\EmailModel;
use App\Payload\Payload;

interface SendMailAdapter
{
    /**
     * Sends an email
     */
    public function __invoke(EmailModel $emailModel) : Payload;
}
