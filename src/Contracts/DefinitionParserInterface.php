<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\ClassDefinition;
use Tochka\Hydrator\DTO\ParameterDefinition;
use Tochka\Hydrator\DTO\PropertyDefinition;
use Tochka\Hydrator\DTO\ValueDefinition;

interface DefinitionParserInterface
{
    /**
     * @param class-string $className
     * @param string $methodName
     * @return array<ParameterDefinition>
     */
    public function getMethodParameters(string $className, string $methodName): array;

    /**
     * @param class-string $className
     * @param string $methodName
     * @return ValueDefinition
     */
    public function getMethodReturn(string $className, string $methodName): ValueDefinition;

    /**
     * @param class-string $className
     * @return array<PropertyDefinition>
     * @throws \ReflectionException
     */
    public function getClassProperties(string $className): array;

    /**
     * @param class-string $className
     * @return ClassDefinition
     */
    public function getClassDefinition(string $className): ClassDefinition;
}
