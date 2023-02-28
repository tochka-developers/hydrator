<?php

namespace Tochka\Hydrator\DTO;

use JetBrains\PhpStorm\Pure;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use Tochka\Hydrator\Support\LegacyEnum;

/**
 * @psalm-api
 */
final class ScalarTypeEnum extends LegacyEnum
{
    private const TYPE_STRING = 'string';
    private const TYPE_FLOAT = 'float';
    private const TYPE_BOOLEAN = 'boolean';
    private const TYPE_INTEGER = 'integer';
    private const TYPE_OBJECT = 'object';
    private const TYPE_ARRAY = 'array';
    private const TYPE_MIXED = 'mixed';

    /**
     * @codeCoverageIgnore
     */
    #[Pure]
    public static function TYPE_STRING(): self
    {
        return new self(self::TYPE_STRING);
    }

    /**
     * @codeCoverageIgnore
     */
    #[Pure]
    public static function TYPE_FLOAT(): self
    {
        return new self(self::TYPE_FLOAT);
    }

    /**
     * @codeCoverageIgnore
     */
    #[Pure]
    public static function TYPE_BOOLEAN(): self
    {
        return new self(self::TYPE_BOOLEAN);
    }

    /**
     * @codeCoverageIgnore
     */
    #[Pure]
    public static function TYPE_INTEGER(): self
    {
        return new self(self::TYPE_INTEGER);
    }

    /**
     * @codeCoverageIgnore
     */
    #[Pure]
    public static function TYPE_OBJECT(): self
    {
        return new self(self::TYPE_OBJECT);
    }

    /**
     * @codeCoverageIgnore
     */
    #[Pure]
    public static function TYPE_ARRAY(): self
    {
        return new self(self::TYPE_ARRAY);
    }

    /**
     * @codeCoverageIgnore
     */
    #[Pure]
    public static function TYPE_MIXED(): self
    {
        return new self(self::TYPE_MIXED);
    }

    /**
     * @param \ReflectionNamedType $type
     * @return static
     */
    #[Pure]
    public static function fromReflectionType(\ReflectionNamedType $type): self
    {
        if (!$type->isBuiltin()) {
            return self::TYPE_OBJECT();
        }

        return match ($type->getName()) {
            'string' => self::TYPE_STRING(),
            'float' => self::TYPE_FLOAT(),
            'bool' => self::TYPE_BOOLEAN(),
            'int' => self::TYPE_INTEGER(),
            'array' => self::TYPE_ARRAY(),
            'object' => self::TYPE_OBJECT(),
            default => self::TYPE_MIXED(),
        };
    }

    #[Pure]
    public static function fromDocBlockType(Type $type): self
    {
        return match (true) {
            $type instanceof String_ => self::TYPE_STRING(),
            $type instanceof Float_ => self::TYPE_FLOAT(),
            $type instanceof Boolean => self::TYPE_BOOLEAN(),
            $type instanceof Integer => self::TYPE_INTEGER(),
            $type instanceof Array_ => self::TYPE_ARRAY(),
            $type instanceof Object_ => self::TYPE_OBJECT(),
            default => self::TYPE_MIXED(),
        };
    }

    #[Pure]
    public static function fromVarType(mixed $var): self
    {
        $varType = gettype($var);

        return match ($varType) {
            'string' => self::TYPE_STRING(),
            'double' => self::TYPE_FLOAT(),
            'boolean' => self::TYPE_BOOLEAN(),
            'integer' => self::TYPE_INTEGER(),
            'array' => self::TYPE_ARRAY(),
            'object' => self::TYPE_OBJECT(),
            default => self::TYPE_MIXED(),
        };
    }

    /**
     * @psalm-mutation-free
     * @return string
     */
    #[Pure]
    public function toJsonType(): string
    {
        return match ($this->getValue()) {
            self::TYPE_STRING => 'string',
            self::TYPE_FLOAT => 'number',
            self::TYPE_BOOLEAN => 'boolean',
            self::TYPE_INTEGER => 'integer',
            self::TYPE_ARRAY => 'array',
            self::TYPE_OBJECT => 'object',
            default => 'any',
        };
    }
}
