<?php

namespace Tochka\Hydrator\Definitions;

use Tochka\Hydrator\Definitions\DTO\ClassDefinition;
use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\Definitions\DTO\DefinitionContainer;
use Tochka\Hydrator\Definitions\DTO\MethodDefinition;

class MethodDefinitionParser
{
    /**
     * @param class-string $className
     * @param string $methodName
     * @return DefinitionContainer<MethodDefinition>
     */
    public function getDefinition(string $className, string $methodName): DefinitionContainer
    {
        $methodDefinition = new MethodDefinition($className, $methodName);
        // TODO: logic for set definitions
        /** @var array<ClassDefinition> $subClassDefinitions */
        $subClassDefinitions = [];

        return new DefinitionContainer($methodDefinition, new Collection($subClassDefinitions));
    }
}
