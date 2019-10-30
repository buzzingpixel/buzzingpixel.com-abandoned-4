<?php

declare(strict_types=1);

namespace App\HttpRespose\Twig\Extensions;

use LogicException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function get_class;
use function gettype;

class RequireVariables extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [$this->getFunction()];
    }

    private function getFunction() : TwigFunction
    {
        return new TwigFunction(
            'requireVariables',
            [$this, 'requireVars'],
            ['needs_context' => true]
        );
    }

    /**
     * @param array<string, mixed>       $context
     * @param array<string, string|null> $required
     *
     * @throws LogicException
     */
    public function requireVars(array $context, array $required) : void
    {
        foreach ($required as $var => $type) {
            if (! isset($context[$var])) {
                $this->throwRequirementException($var, $type ?: null);
            }

            if (! $type) {
                continue;
            }

            /** @psalm-suppress MixedAssignment */
            $val = $context[$var];

            $varType = gettype($val);

            if ($varType === 'object' && $val) {
                /** @psalm-suppress MixedArgument */
                $varType = get_class($val);
            }

            if ($varType === $type) {
                continue;
            }

            $this->throwRequirementException($var, $type ?: null);
        }
    }

    /**
     * @throws LogicException
     */
    private function throwRequirementException(string $var, ?string $type) : void
    {
        $message = 'Variable "' . $var . '" is required';

        if ($type) {
            $message .= ' and must be of type "' . $type . '"';
        }

        throw new LogicException($message);
    }
}
