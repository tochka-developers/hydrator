<?php

namespace Tochka\Hydrator\Support;

use Tochka\Hydrator\Contracts\TypeResolverInterface;
use Tochka\Hydrator\DTO\ScalarTypeEnum;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\UnionTypeDefinition;

/**
 * @psalm-import-type ClassMatcher from TypeResolverInterface
 * @psalm-import-type InterfaceMatcher from TypeResolverInterface
 */
class TypeResolver implements TypeResolverInterface
{
    /** @var array<class-string, ClassMatcher> */
    private array $classMatchers = [];
    /** @var array<class-string, InterfaceMatcher> */
    private array $interfaceMatchers = [];

    public function addClassMatcher(string $className, callable $match): void
    {
        $this->classMatchers[$className] = $match;
    }

    public function addInterfaceMatcher(string $interfaceName, callable $match): void
    {
        $this->interfaceMatchers[$interfaceName] = $match;
    }

    public function resolve(
        mixed $valueToResolve,
        TypeDefinition|UnionTypeDefinition $typeDefinition
    ): ?TypeDefinition {
        if ($typeDefinition instanceof UnionTypeDefinition) {
            $actualScalarType = ScalarTypeEnum::fromVarType($valueToResolve);

            foreach ($typeDefinition->getTypes() as $type) {
                if ($type->getScalarType() !== ScalarTypeEnum::TYPE_OBJECT) {
                    if ($actualScalarType === $type->getScalarType()) {
                        return $type;
                    }
                } elseif ($type->getClassName() !== null && is_object($valueToResolve)) {
                    if (
                        array_key_exists($type->getClassName(), $this->classMatchers)
                        && $this->classMatchers[$type->getClassName()]($valueToResolve)
                    ) {
                        return $type;
                    } elseif (array_key_exists($type->getClassName(), $this->interfaceMatchers)) {
                        return $this->resolve($valueToResolve, $type);
                    }
                }
            }
        } elseif (
            $typeDefinition->getScalarType() === ScalarTypeEnum::TYPE_OBJECT
            && $typeDefinition->getClassName() !== null
            && is_object($valueToResolve)
            && array_key_exists($typeDefinition->getClassName(), $this->interfaceMatchers)
        ) {
            $className = $this->interfaceMatchers[$typeDefinition->getClassName()]($valueToResolve);
            $resultType = clone $typeDefinition;
            $resultType->setNeedResolve(false);
            $resultType->setClassName($className);

            return $resultType;
        }

        return null;
    }
}
