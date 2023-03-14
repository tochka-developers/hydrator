<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Definitions;

use Tochka\Hydrator\Attributes\Ignore;
use Tochka\Hydrator\Contracts\ClassDefinitionParserInterface;
use Tochka\Hydrator\Contracts\ClassDefinitionsRegistryInterface;
use Tochka\Hydrator\Contracts\ExtendedReflectionFactoryInterface;
use Tochka\Hydrator\Contracts\MethodDefinitionParserInterface;
use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\Definitions\DTO\MethodDefinition;
use Tochka\Hydrator\Definitions\DTO\ReturnDefinition;
use Tochka\Hydrator\Exceptions\MethodNotDefinedExceptionBase;
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
            throw new MethodNotDefinedExceptionBase(sprintf('Method [%s::%s] is not defined', $className, $methodName));
        }

        $methodDefinition->attributes = $reflection->getAttributes();
        $methodDefinition->description = $reflection->getDescription();

        $parameters = [];
        foreach ($reflection->getParameters() as $parameterReflection) {
            if ($parameterReflection->getAttributes()->has(Ignore::class)) {
                continue;
            }

            $parameter = $this->getValueDefinition($parameterReflection);
            $this->getClassDefinitionsFromType($this->classDefinitionParser, $parameter->type);

            $parameters[] = $parameter;
        }

        $methodDefinition->parameters = new Collection($parameters);
        $methodDefinition->returnDefinition = $this->getReturnDefinition($reflection);

        return $methodDefinition;
    }

    public function getReturnDefinition(ExtendedMethodReflection $reflection): ReturnDefinition
    {
        $definition = new ReturnDefinition($reflection->getReturnType());
        $definition->description = $reflection->getReturnDescription();

        return $definition;
    }
}
