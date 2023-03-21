<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\Definitions\DTO\ClassDefinition;

/**
 * @psalm-api
 */
interface ClassDefinitionParserInterface
{
    /**
     * @param class-string $className
     */
    public function getDefinition(string $className): ClassDefinition;
}
