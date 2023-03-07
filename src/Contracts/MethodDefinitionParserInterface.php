<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\Definitions\DTO\MethodDefinition;

interface MethodDefinitionParserInterface
{
    /**
     * @param class-string $className
     */
    public function getDefinition(string $className, string $methodName): MethodDefinition;
}
