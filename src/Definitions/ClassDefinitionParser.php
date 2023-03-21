<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Definitions;

use Tochka\Hydrator\Attributes\Ignore;
use Tochka\Hydrator\Contracts\ClassDefinitionParserInterface;
use Tochka\Hydrator\Contracts\ClassDefinitionsRegistryInterface;
use Tochka\Hydrator\Definitions\DTO\ClassDefinition;
use Tochka\TypeParser\Collection;
use Tochka\TypeParser\Contracts\ExtendedReflectionFactoryInterface;

/**
 * @psalm-api
 */
class ClassDefinitionParser implements ClassDefinitionParserInterface
{
    use GetValueDefinition;

    public function __construct(
        private readonly ExtendedReflectionFactoryInterface $reflectionFactory,
        private readonly ClassDefinitionsRegistryInterface $classDefinitionsRegistry,
    ) {
    }

    /**
     * @param class-string $className
     */
    public function getDefinition(string $className): ClassDefinition
    {
        if ($this->classDefinitionsRegistry->has($className)) {
            /** @var ClassDefinition */
            return $this->classDefinitionsRegistry->get($className);
        }

        $classDefinition = new ClassDefinition($className);
        $this->classDefinitionsRegistry->add($classDefinition);

        try {
            $reflection = $this->reflectionFactory->makeForClass($className);
        } catch (\ReflectionException) {
            $classDefinition->virtual = true;
            return $classDefinition;
        }

        $classDefinition->attributes = $reflection->getAttributes();
        $classDefinition->summary = $reflection->getSummary();
        $classDefinition->description = $reflection->getDescription();

        $properties = [];
        foreach ($reflection->getProperties() as $propertyReflection) {
            if ($propertyReflection->getAttributes()->has(Ignore::class)) {
                continue;
            }

            $property = $this->getValueDefinition($propertyReflection);
            $this->getClassDefinitionsFromType($this, $property->type);

            $properties[] = $property;
        }

        $classDefinition->properties = new Collection($properties);
        $classDefinition->isEnum = enum_exists($className);
        $classDefinition->isInterface = interface_exists($className);
        $classDefinition->isTrait = trait_exists($className);

        return $classDefinition;
    }
}
