<?php

namespace Tochka\Hydrator\Definitions;

use Tochka\Hydrator\Contracts\ClassDefinitionParserInterface;
use Tochka\Hydrator\Definitions\DTO\ValueDefinition;
use Tochka\Hydrator\ExtendedReflection\ExtendedValueReflectionInterface;
use Tochka\Hydrator\TypeSystem\TypeInterface;
use Tochka\Hydrator\TypeSystem\Types\ArrayType;
use Tochka\Hydrator\TypeSystem\Types\IntersectionType;
use Tochka\Hydrator\TypeSystem\Types\NamedObjectType;
use Tochka\Hydrator\TypeSystem\Types\UnionType;

trait GetValueDefinition
{
    public function getValueDefinition(ExtendedValueReflectionInterface $reflection): ValueDefinition
    {
        $property = new ValueDefinition($reflection->getName(), $reflection->getType());
        $property->setAttributes($reflection->getAttributes());
        $property->setRequired($reflection->isRequired());
        if ($reflection->hasDefaultValue()) {
            $property->setDefaultValue($reflection->getDefaultValue());
        }

        $description = $reflection->getDescription();
        if ($description !== null) {
            $property->setDescription($description);
        }

        return $property;
    }

    private function getClassDefinitionsFromType(ClassDefinitionParserInterface $parser, TypeInterface $type): void
    {
        if ($type instanceof UnionType || $type instanceof IntersectionType) {
            foreach ($type->types as $type) {
                $this->getClassDefinitionsFromType($parser, $type);
            }
        }

        if ($type instanceof NamedObjectType) {
            $parser->getDefinition($type->className);
        }

        if ($type instanceof ArrayType) {
            $this->getClassDefinitionsFromType($parser, $type->valueType);
        }
    }
}
