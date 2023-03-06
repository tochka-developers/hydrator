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
use Tochka\Hydrator\Support\OneOfEnum;

/**
 * @psalm-api
 */
enum ScalarTypeEnum: string
{
    use OneOfEnum;

    case TYPE_STRING = 'string';
    case TYPE_FLOAT = 'float';
    case TYPE_BOOLEAN = 'boolean';
    case TYPE_INTEGER = 'integer';
    case TYPE_OBJECT = 'object';
    case TYPE_ARRAY = 'array';
    case TYPE_MIXED = 'mixed';

    #[Pure]
    public static function fromReflectionType(\ReflectionNamedType $type): self
    {
        if (!$type->isBuiltin()) {
            return self::TYPE_OBJECT;
        }

        return match ($type->getName()) {
            'string' => self::TYPE_STRING,
            'float' => self::TYPE_FLOAT,
            'bool' => self::TYPE_BOOLEAN,
            'int' => self::TYPE_INTEGER,
            'array' => self::TYPE_ARRAY,
            'object' => self::TYPE_OBJECT,
            default => self::TYPE_MIXED,
        };
    }

    #[Pure]
    public static function fromDocBlockType(Type $type): self
    {
        return match (true) {
            $type instanceof String_ => self::TYPE_STRING,
            $type instanceof Float_ => self::TYPE_FLOAT,
            $type instanceof Boolean => self::TYPE_BOOLEAN,
            $type instanceof Integer => self::TYPE_INTEGER,
            $type instanceof Array_ => self::TYPE_ARRAY,
            $type instanceof Object_ => self::TYPE_OBJECT,
            default => self::TYPE_MIXED,
        };
    }

    #[Pure]
    public static function fromVarType(mixed $var): self
    {
        $varType = gettype($var);

        return match ($varType) {
            'string' => self::TYPE_STRING,
            'double' => self::TYPE_FLOAT,
            'boolean' => self::TYPE_BOOLEAN,
            'integer' => self::TYPE_INTEGER,
            'array' => self::TYPE_ARRAY,
            'object' => self::TYPE_OBJECT,
            default => self::TYPE_MIXED,
        };
    }

    /**
     * @psalm-mutation-free
     * @return string
     */
    #[Pure]
    public function toJsonType(): string
    {
        return match ($this) {
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
