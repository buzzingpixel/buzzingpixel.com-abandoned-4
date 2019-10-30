<?php

declare(strict_types=1);

namespace App\Payload;

use InvalidArgumentException;
use LogicException;
use ReflectionClass;
use ReflectionException;
use function method_exists;
use function ucfirst;

abstract class SpecificPayload
{
    /** @var bool */
    protected $isInitialized = false;

    /**
     * @param mixed[] $vars
     *
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws LogicException
     */
    public function __construct(array $vars = [])
    {
        if ($this->isInitialized) {
            throw new LogicException(
                static::class . ' instances can only be initialized once.'
            );
        }

        $this->isInitialized = true;

        /** @psalm-suppress MixedAssignment */
        foreach ($vars as $var => $val) {
            /** @var string $var */

            $method = 'set' . ucfirst($var);

            if (! method_exists($this, $method)) {
                throw new InvalidArgumentException(
                    'Property does not exist: ' . $var
                );
            }

            $this->{$method}($val);
        }

        $reflect = new ReflectionClass($this);

        $this->shortName = $reflect->getShortName();
    }

    /** @var string $shortName */
    protected $shortName = '';

    public function getShortName() : string
    {
        return $this->shortName;
    }
}
