<?php

declare(strict_types=1);

namespace App\Persistence;

use ReflectionClass;
use ReflectionProperty;
use Throwable;
use function array_map;
use function lcfirst;
use function mb_strlen;
use function mb_strpos;
use function mb_substr;
use function strrev;

abstract class Record
{
    protected static string $tableName = '';

    public string $id = '';

    public function getTableName() : string
    {
        try {
            if (static::$tableName !== '') {
                return static::$tableName;
            }

            $reflectionClass = new ReflectionClass($this);

            $shortName = (string) $reflectionClass->getShortName();

            $rev = strrev($shortName);

            if (mb_strpos($rev, 'droceR') !== 0) {
                return $shortName;
            }

            $shortNameLength = (int) mb_strlen($shortName);

            return lcfirst((string) (mb_substr($shortName, 0, $shortNameLength - 6))) . 's';

            // @codeCoverageIgnoreStart
        } catch (Throwable $e) {
            return '';
        }

        // @codeCoverageIgnoreEnd
    }

    /**
     * @return string[]
     */
    public function getFields(bool $prefixNamesForBind = false) : array
    {
        $prefixNamesWith = $prefixNamesForBind ? ':' : '';

        try {
            /** @noinspection PhpUnhandledExceptionInspection */
            $reflectionClass = new ReflectionClass($this);

            $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);

            return array_map(
                static function (ReflectionProperty $property) use ($prefixNamesWith) {
                    return $prefixNamesWith . $property->getName();
                },
                $properties
            );

            // @codeCoverageIgnoreStart
        } catch (Throwable $e) {
            return [];
        }

        // @codeCoverageIgnoreEnd
    }

    /**
     * @return array<string, mixed>
     */
    public function getBindValues() : array
    {
        $bindValues = [];

        foreach ($this->getFields() as $field) {
            /** @psalm-suppress MixedAssignment */
            $bindValues[':' . $field] = $this->{$field};
        }

        return $bindValues;
    }
}
