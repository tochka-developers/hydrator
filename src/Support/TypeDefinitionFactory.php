<?php

namespace Tochka\Hydrator\Support;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\AbstractList;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Object_;
use Tochka\Hydrator\Contracts\TypeDefinitionFactoryInterface;
use Tochka\Hydrator\DTO\ScalarTypeEnum;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\UnionTypeDefinition;

class TypeDefinitionFactory implements TypeDefinitionFactoryInterface
{
    use FullyQualifiedClassName;

    /**
     * @throws \ReflectionException
     */
    public function getFromReflection(?\ReflectionType $reflectionType): TypeDefinition|UnionTypeDefinition
    {
        if ($reflectionType instanceof \ReflectionUnionType) {
            $typeDefinitions = [];

            foreach ($reflectionType->getTypes() as $reflectionNamedType) {
                if ($reflectionNamedType->getName() === 'null') {
                    continue;
                }

                $typeDefinitions[] = $this->getParameterTypeFromReflection($reflectionNamedType);
            }

            $typeDefinition = new UnionTypeDefinition($typeDefinitions);
            $typeDefinition->setNullable($reflectionType->allowsNull());
        } elseif ($reflectionType instanceof \ReflectionNamedType) {
            $typeDefinition = $this->getParameterTypeFromReflection($reflectionType);
            $typeDefinition->setNullable($reflectionType->allowsNull());
        } else {
            $typeDefinition = new TypeDefinition(ScalarTypeEnum::TYPE_MIXED);
            $typeDefinition->setNullable(true);
        }

        return $typeDefinition;
    }

    /**
     * @throws \ReflectionException
     */
    private function getParameterTypeFromReflection(\ReflectionNamedType $reflectionType): TypeDefinition
    {
        $typeDefinition = new TypeDefinition(ScalarTypeEnum::fromReflectionType($reflectionType));

        if ($typeDefinition->getScalarType() === ScalarTypeEnum::TYPE_OBJECT && !$reflectionType->isBuiltin()) {
            $typeDefinition->setClassName($reflectionType->getName());

            if (interface_exists($reflectionType->getName())) {
                $typeDefinition->setNeedResolve(true);
            }
            $reflectionClass = new \ReflectionClass($reflectionType->getName());
            if ($reflectionClass->isAbstract()) {
                $typeDefinition->setNeedResolve(true);
            }
        }

        return $typeDefinition;
    }

    public function getFromDocBlock(?Type $typeDefinition): TypeDefinition|UnionTypeDefinition
    {
        if ($typeDefinition === null) {
            return new TypeDefinition(ScalarTypeEnum::TYPE_MIXED);
        }

        if ($typeDefinition instanceof Compound) {
            $typeDefinitions = [];
            $nullable = false;
            foreach ($typeDefinition->getIterator() as $inboundType) {
                if ($inboundType instanceof Null_) {
                    $nullable = true;
                }
                $typeDefinitions[] = $this->getParameterTypeFromDocBlock($inboundType);
            }

            $typeDefinition = new UnionTypeDefinition($typeDefinitions);
            $typeDefinition->setNullable($nullable);

            return $typeDefinition;
        }

        return $this->getParameterTypeFromDocBlock($typeDefinition);
    }

    private function getParameterTypeFromDocBlock(Type $type): TypeDefinition
    {
        $scalarType = ScalarTypeEnum::fromDocBlockType($type);
        $typeDefinition = new TypeDefinition($scalarType);

        if ($type instanceof AbstractList) {
            if ($type instanceof Collection) {
                $typeDefinition->setClassName($this->fullyQualifiedClassName((string)$type->getFqsen()));
            }
            $typeDefinition->setValueType($this->getFromDocBlock($type->getValueType()));
        } elseif ($type instanceof Object_) {
            $typeDefinition->setClassName($this->fullyQualifiedClassName((string)$type->getFqsen()));
        } elseif ($type instanceof Null_) {
            $typeDefinition->setNullable(true);
        }

        return $typeDefinition;
    }
}
