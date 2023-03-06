<?php

namespace Tochka\Hydrator\ExtendedReflection\TypeFactories;

use Tochka\Hydrator\ExtendedReflection\ExtendedReflectionWithTypeInterface;
use Tochka\Hydrator\TypeSystem\TypeInterface;
use Tochka\Hydrator\TypeSystem\Types\ArrayType;
use Tochka\Hydrator\TypeSystem\Types\BoolType;
use Tochka\Hydrator\TypeSystem\Types\CallableType;
use Tochka\Hydrator\TypeSystem\Types\FalseType;
use Tochka\Hydrator\TypeSystem\Types\FloatType;
use Tochka\Hydrator\TypeSystem\Types\IntersectionType;
use Tochka\Hydrator\TypeSystem\Types\IntType;
use Tochka\Hydrator\TypeSystem\Types\MixedType;
use Tochka\Hydrator\TypeSystem\Types\NamedObjectType;
use Tochka\Hydrator\TypeSystem\Types\NeverType;
use Tochka\Hydrator\TypeSystem\Types\NullType;
use Tochka\Hydrator\TypeSystem\Types\ObjectType;
use Tochka\Hydrator\TypeSystem\Types\ResourceType;
use Tochka\Hydrator\TypeSystem\Types\StringType;
use Tochka\Hydrator\TypeSystem\Types\TrueType;
use Tochka\Hydrator\TypeSystem\Types\UnionType;
use Tochka\Hydrator\TypeSystem\Types\VoidType;

class ReflectionTypeFactoryMiddleware implements TypeFactoryMiddlewareInterface
{
    public function handle(
        TypeInterface $defaultType,
        ExtendedReflectionWithTypeInterface $reflector,
        callable $next
    ): TypeInterface {
        $originalReflector = $reflector->getReflection();

        if (!$originalReflector instanceof \ReflectionParameter && !$originalReflector instanceof \ReflectionProperty) {
            return $next($defaultType, $reflector);
        }

        $reflectionType = $originalReflector->getType();
        if ($reflectionType === null) {
            return $next($defaultType, $reflector);
        }

        return $next(
            $this->getType($reflectionType) ?? $defaultType,
            $reflector
        );
    }

    private function getType(\ReflectionType $reflectionType): ?TypeInterface
    {
        if ($reflectionType instanceof \ReflectionUnionType) {
            return new UnionType(...$this->getMultipleTypes($reflectionType));
        }
        if ($reflectionType instanceof \ReflectionIntersectionType) {
            return new IntersectionType(...$this->getMultipleTypes($reflectionType));
        }
        if ($reflectionType instanceof \ReflectionNamedType) {
            $type = $this->getNamedType($reflectionType);
            if ($reflectionType->allowsNull()) {
                return new UnionType(new NullType(), $type);
            }

            return $type;
        }

        return null;
    }

    /**
     * @return non-empty-list<TypeInterface>
     */
    private function getMultipleTypes(\ReflectionUnionType|\ReflectionIntersectionType $reflectionUnionType): array
    {
        return array_filter(
            array_map(
                fn (\ReflectionType $type): TypeInterface => $this->getType($type),
                $reflectionUnionType->getTypes()
            )
        );
    }

    private function getNamedType(\ReflectionNamedType $reflectionType): TypeInterface
    {
        return match ($reflectionType->getName()) {
            'array', 'iterable' => new ArrayType(),
            'bool' => new BoolType(),
            'callable', 'Closure', '\Closure' => new CallableType(),
            'false' => new FalseType(),
            'float' => new FloatType(),
            'int' => new IntType(),
            'mixed' => new MixedType(),
            'never' => new NeverType(),
            'null' => new NullType(),
            'object' => new ObjectType(),
            'resource' => new ResourceType(),
            'string' => new StringType(),
            'true' => new TrueType(),
            'void' => new VoidType(),
            default => new NamedObjectType($reflectionType->getName())
        };
    }
}
