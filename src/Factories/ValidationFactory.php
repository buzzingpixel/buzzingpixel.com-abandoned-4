<?php

declare(strict_types=1);

namespace App\Factories;

use Awurth\SlimValidation\Validator;

class ValidationFactory
{
    /**
     * @param string[] $defaultMessages
     */
    public function __invoke(array $defaultMessages = []) : Validator
    {
        return new Validator(
            false,
            $defaultMessages
        );
    }
}
