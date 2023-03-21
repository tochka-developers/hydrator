<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Definitions;

use Tochka\Hydrator\Contracts\ClassDefinitionParserInterface;
use Tochka\Hydrator\Definitions\DTO\ValueDefinition;
use Tochka\TypeParser\Contracts\ExtendedValueReflectionInterface;
use Tochka\TypeParser\TypeSystem\TypeInterface;
use Tochka\TypeParser\TypeSystem\Types\ArrayType;
use Tochka\TypeParser\TypeSystem\Types\IntersectionType;
use Tochka\TypeParser\TypeSystem\Types\NamedObjectType;
use Tochka\TypeParser\TypeSystem\Types\UnionType;

trait GetValueDefinition
{
    public function getValueDefinition(ExtendedValueReflectionInterface $reflection): ValueDefinition
    {
        $property = new ValueDefinition($reflection->getName(), $reflection->getType());
        $property->attributes = $reflection->getAttributes();
        $property->required = $reflection->isRequired();
        if ($reflection->hasDefaultValue()) {
            $property->setDefaultValue($reflection->getDefaultValue());
        }

        $property->summary = $reflection->getSummary();
        $property->description = $reflection->getDescription();

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
