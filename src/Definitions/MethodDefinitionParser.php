<?php

namespace Tochka\Hydrator\Definitions;

use Tochka\Hydrator\Contracts\ClassDefinitionParserInterface;
use Tochka\Hydrator\Contracts\ClassDefinitionsRegistryInterface;
use Tochka\Hydrator\Contracts\ExtendedReflectionFactoryInterface;
use Tochka\Hydrator\Contracts\MethodDefinitionParserInterface;
use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\Definitions\DTO\MethodDefinition;
use Tochka\Hydrator\Definitions\DTO\ReturnDefinition;
use Tochka\Hydrator\Exceptions\MethodNotDefinedException;
use Tochka\Hydrator\ExtendedReflection\Reflectors\ExtendedMethodReflection;

class MethodDefinitionParser implements MethodDefinitionParserInterface
{
    use GetValueDefinition;

    private ExtendedReflectionFactoryInterface $reflectionFactory;
    private ClassDefinitionParserInterface $classDefinitionParser;
    private ClassDefinitionsRegistryInterface $classDefinitionsRegistry;

    public function __construct(
        ClassDefinitionsRegistryInterface $classDefinitionsRegistry,
        ExtendedReflectionFactoryInterface $reflectionFactory,
        ClassDefinitionParserInterface $classDefinitionParser,
    ) {
        $this->reflectionFactory = $reflectionFactory;
        $this->classDefinitionParser = $classDefinitionParser;
        $this->classDefinitionsRegistry = $classDefinitionsRegistry;
    }

    /**
     * @param class-string $className
     */
    public function getDefinition(string $className, string $methodName): MethodDefinition
    {
        $methodDefinition = new MethodDefinition($className, $methodName);
        try {
            $reflection = $this->reflectionFactory->makeForMethod($className, $methodName);
        } catch (\ReflectionException) {
            throw new MethodNotDefinedException(sprintf('Method [%s::%s] is not defined', $className, $methodName));
        }

        $methodDefinition->setAttributes($reflection->getAttributes());
        $description = $reflection->getDescription();
        if ($description !== null) {
            $methodDefinition->setDescription($description);
        }

        $parameters = [];
        foreach ($reflection->getParameters() as $parameterReflection) {
            $parameter = $this->getValueDefinition($parameterReflection);
            $this->getClassDefinitionsFromType($this->classDefinitionParser, $parameter->getType());

            $parameters[] = $parameter;
        }

        $methodDefinition->setParameters(new Collection($parameters));
        $methodDefinition->setReturnDefinition($this->getReturnDefinition($reflection));

        return $methodDefinition;
    }

    public function getReturnDefinition(ExtendedMethodReflection $reflection): ReturnDefinition
    {
        $definition = new ReturnDefinition($reflection->getReturnType());
        $description = $reflection->getReturnDescription();
        if ($description !== null) {
            $definition->setDescription($description);
        }

        return $definition;
    }
}
