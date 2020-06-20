<?php

declare(strict_types=1);

namespace App\HttpResponse\Twig\Extensions;

use LogicException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use function get_class;
use function gettype;
use function is_string;

class RequireVariables extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [$this->getFunction()];
    }

    private function getFunction(): TwigFunction
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
    public function requireVars(array $context, array $required): void
    {
        foreach ($required as $var => $type) {
            if (! isset($context[$var])) {
                /** @phpstan-ignore-next-line */
                $this->throwRequirementException($var, $type ?: null);
            }

            if ($type === '' || $type === null) {
                // This code is totally running but code coverage says it's not
                // @codeCoverageIgnoreStart
                continue;

                // @codeCoverageIgnoreEnd
            }

            /**
             * @psalm-suppress MixedAssignment
             * @psalm-suppress PossiblyUndefinedArrayOffset
             */
            $val = $context[$var];

            $varType = gettype($val);

            if ($varType === 'object') {
                // This code is totally running but code coverage says it's not
                // @codeCoverageIgnoreStart
                /**
                 * Hey Psalm, do you see this condition right here?
                 * We know it can't be null
                 *
                 * @psalm-suppress MixedArgument
                 * @psalm-suppress PossiblyNullArgument
                 */
                $varType = get_class($val);

                // @codeCoverageIgnoreEnd
            }

            if ($varType === $type) {
                // This code is totally running but code coverage says it's not
                // @codeCoverageIgnoreStart
                continue;

                // @codeCoverageIgnoreEnd
            }

            /**
             * @psalm-suppress DocblockTypeContradiction
             */
            $this->throwRequirementException(
                $var,
                /** @phpstan-ignore-next-line */
                is_string($type) ? $type : null
            );
        }

        // @codeCoverageIgnoreStart
    }

    // @codeCoverageIgnoreEnd

    /**
     * @throws LogicException
     */
    private function throwRequirementException(string $var, ?string $type): void
    {
        $message = 'Variable "' . $var . '" is required';

        if ($type !== null && $type !== '') {
            $message .= ' and must be of type "' . $type . '"';
        }

        throw new LogicException($message);
    }
}
