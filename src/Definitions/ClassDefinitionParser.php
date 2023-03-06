<?php

namespace Tochka\Hydrator\Definitions;

use Tochka\Hydrator\Contracts\ExtendedReflectionFactoryInterface;
use Tochka\Hydrator\Definitions\DTO\ClassDefinition;
use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\Definitions\DTO\PropertyDefinition;
use Tochka\Hydrator\ExtendedReflection\Reflectors\ExtendedPropertyReflection;
use Tochka\Hydrator\TypeSystem\TypeInterface;
use Tochka\Hydrator\TypeSystem\Types\ArrayType;
use Tochka\Hydrator\TypeSystem\Types\IntersectionType;
use Tochka\Hydrator\TypeSystem\Types\NamedObjectType;
use Tochka\Hydrator\TypeSystem\Types\UnionType;

class ClassDefinitionParser
{
    private ExtendedReflectionFactoryInterface $reflectionFactory;
    /** @var array<class-string, ClassDefinition> */
    private array $classDefinitions = [];

    public function __construct(ExtendedReflectionFactoryInterface $reflectionFactory)
    {
        $this->reflectionFactory = $reflectionFactory;
    }

    /**
     * @param class-string $className
     */
    public function getDefinition(string $className): ClassDefinition
    {
        $classDefinition = new ClassDefinition($className);
        $this->classDefinitions[$className] = $classDefinition;

        try {
            $reflection = $this->reflectionFactory->makeForClass($className);
        } catch (\ReflectionException) {
            $classDefinition->setVirtual(true);
            return $classDefinition;
        }

        $classDefinition->setAttributes($reflection->getAttributes());

        $description = $reflection->getDescription();
        if ($description !== null) {
            $classDefinition->setDescription($description);
        }

        $properties = [];
        foreach ($reflection->getProperties() as $propertyReflection) {
            $properties[] = $this->getPropertyDefinition($propertyReflection);
        }

        $classDefinition->setProperties(new Collection($properties));

        return $classDefinition;
    }

    public function getPropertyDefinition(ExtendedPropertyReflection $reflection): PropertyDefinition
    {
        $property = new PropertyDefinition($reflection->getName(), $reflection->getType());
        $property->setAttributes($reflection->getAttributes());
        $property->setRequired($reflection->isRequired());
        if ($reflection->hasDefaultValue()) {
            $property->setDefaultValue($reflection->getDefaultValue());
        }

        $description = $reflection->getDescription();
        if ($description !== null) {
            $property->setDescription($description);
        }

        $this->getClassDefinitionsFromType($property->getType());

        return $property;
    }

    public function getClassDefinitions(): array
    {
        return $this->classDefinitions;
    }


    private function getClassDefinitionsFromType(TypeInterface $type): void
    {
        if ($type instanceof UnionType || $type instanceof IntersectionType) {
            foreach ($type->types as $type) {
                $this->getClassDefinitionsFromType($type);
            }
        }

        if ($type instanceof NamedObjectType) {
            $this->parseClassIfNecessary($type->className);
        }

        if ($type instanceof ArrayType) {
            $this->getClassDefinitionsFromType($type->valueType);
        }
    }

    /**
     * @param class-string $className
     */
    private function parseClassIfNecessary(string $className): void
    {
        if (!array_key_exists($className, $this->classDefinitions)) {
            $this->getDefinition($className);
        }
    }
}
