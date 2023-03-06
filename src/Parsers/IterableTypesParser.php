<?php

namespace Tochka\Hydrator\Parsers;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\AbstractList;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\Compound;
use Tochka\Hydrator\Contracts\TypeDefinitionFactoryInterface;
use Tochka\Hydrator\DTO\CallableTypeInterface;
use Tochka\Hydrator\DTO\ScalarTypeEnum;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\Support\FullyQualifiedClassName;

class IterableTypesParser
{
    use FullyQualifiedClassName;

    private TypeDefinitionFactoryInterface $parameterTypeFactory;

    public function __construct(TypeDefinitionFactoryInterface $parameterTypeFactory)
    {
        $this->parameterTypeFactory = $parameterTypeFactory;
    }

    public function parse(CallableTypeInterface $type, ?Type $docBlockType): void
    {
        $type->call(
            fn (TypeDefinition $type) => $this->parseParameterType($type, $docBlockType)
        );
    }

    private function parseParameterType(TypeDefinition $type, ?Type $docBlockType): void
    {
        if ($type->getScalarType() === ScalarTypeEnum::TYPE_ARRAY) {
            $valueType = $this->getValueTypeForArray($docBlockType);
            $type->setValueType($this->parameterTypeFactory->getFromDocBlock($valueType));
        } elseif ($type->getScalarType() === ScalarTypeEnum::TYPE_OBJECT) {
            if ($type->getClassName() === null) {
                return;
            }

            $valueType = $this->getValueTypeForCollection($type->getClassName(), $docBlockType);
            if ($valueType !== null) {
                $type->setValueType($this->parameterTypeFactory->getFromDocBlock($valueType));
            }
        }
    }

    private function getValueTypeForArray(?Type $docBlockType): ?Type
    {
        if ($docBlockType === null) {
            return null;
        }

        if ($docBlockType instanceof Compound) {
            foreach ($docBlockType->getIterator() as $docBlockTypeInCompound) {
                if ($docBlockTypeInCompound instanceof AbstractList && !$docBlockTypeInCompound instanceof Collection) {
                    return $docBlockTypeInCompound->getValueType();
                }
            }
        } elseif ($docBlockType instanceof AbstractList && !$docBlockType instanceof Collection) {
            return $docBlockType->getValueType();
        }

        return null;
    }

    private function getValueTypeForCollection(string $className, ?Type $docBlockType): ?Type
    {
        if ($docBlockType === null) {
            return null;
        }

        if (!is_a($className, \Traversable::class, true)) {
            return null;
        }

        if ($docBlockType instanceof Compound) {
            foreach ($docBlockType->getIterator() as $typeInCompound) {
                if (
                    $typeInCompound instanceof Collection
                    && $this->fullyQualifiedClassName($typeInCompound->getFqsen()) === $className
                ) {
                    return $typeInCompound->getValueType();
                }
            }
        }
        if (
            $docBlockType instanceof Collection
            && $this->fullyQualifiedClassName($docBlockType->getFqsen()) === $className
        ) {
            return $docBlockType->getValueType();
        }

        return null;
    }
}
