<?php

namespace Tochka\Hydrator\Definitions;

use Tochka\Hydrator\Contracts\ClassDefinitionParserInterface;
use Tochka\Hydrator\Contracts\ClassDefinitionsRegistryInterface;
use Tochka\Hydrator\Contracts\ExtendedReflectionFactoryInterface;
use Tochka\Hydrator\Definitions\DTO\ClassDefinition;
use Tochka\Hydrator\Definitions\DTO\Collection;

class ClassDefinitionParser implements ClassDefinitionParserInterface
{
    use GetValueDefinition;

    private ExtendedReflectionFactoryInterface $reflectionFactory;
    private ClassDefinitionsRegistryInterface $classDefinitionsRegistry;

    public function __construct(
        ExtendedReflectionFactoryInterface $reflectionFactory,
        ClassDefinitionsRegistryInterface $classDefinitionsRegistry,
    ) {
        $this->reflectionFactory = $reflectionFactory;
        $this->classDefinitionsRegistry = $classDefinitionsRegistry;
    }

    /**
     * @param class-string $className
     */
    public function getDefinition(string $className): ClassDefinition
    {
        if ($this->classDefinitionsRegistry->has($className)) {
            return $this->classDefinitionsRegistry->get($className);
        }

        $classDefinition = new ClassDefinition($className);
        $this->classDefinitionsRegistry->add($classDefinition);

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
            $property = $this->getValueDefinition($propertyReflection);
            $this->getClassDefinitionsFromType($this, $property->getType());

            $properties[] = $property;
        }

        $classDefinition->setProperties(new Collection($properties));
        $classDefinition->setIsEnum(enum_exists($className));
        $classDefinition->setIsInterface(interface_exists($className));
        $classDefinition->setIsTrait(trait_exists($className));

        return $classDefinition;
    }
}
