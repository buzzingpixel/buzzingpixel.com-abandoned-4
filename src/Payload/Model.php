<?php

declare(strict_types=1);

namespace App\Payload;

use InvalidArgumentException;
use function method_exists;
use function ucfirst;

abstract class Model
{
    /**
     * @param mixed[] $vars
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $vars = [])
    {
        /** @psalm-suppress MixedAssignment */
        foreach ($vars as $var => $val) {
            /** @var string $var */

            $method = 'set' . ucfirst($var);

            if (! method_exists($this, $method)) {
                throw new InvalidArgumentException('Property does not exist: ' . $var);
            }

            $this->{$method}($val);
        }
    }
}