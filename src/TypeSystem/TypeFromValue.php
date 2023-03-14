<?php

declare(strict_types=1);

namespace Tochka\Hydrator\TypeSystem;

use Tochka\Hydrator\TypeSystem\Types\ArrayType;
use Tochka\Hydrator\TypeSystem\Types\BoolType;
use Tochka\Hydrator\TypeSystem\Types\FloatType;
use Tochka\Hydrator\TypeSystem\Types\IntType;
use Tochka\Hydrator\TypeSystem\Types\MixedType;
use Tochka\Hydrator\TypeSystem\Types\NullType;
use Tochka\Hydrator\TypeSystem\Types\ObjectType;
use Tochka\Hydrator\TypeSystem\Types\ResourceType;
use Tochka\Hydrator\TypeSystem\Types\StringType;

class TypeFromValue
{
    public function inferType(mixed $value): TypeInterface
    {
        $type = gettype($value);

        return match ($type) {
            'array' => new ArrayType(),
            'boolean' => new BoolType(),
            'double' => new FloatType(),
            'integer' => new IntType(),
            'NULL' => new NullType(),
            'object' => new ObjectType(),
            'resource', 'resource (closed)' => new ResourceType(),
            'string' => new StringType(),
            default => new MixedType(),
        };
    }
}
