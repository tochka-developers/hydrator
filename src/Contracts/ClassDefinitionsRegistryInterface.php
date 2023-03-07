<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\Definitions\DTO\ClassDefinition;

interface ClassDefinitionsRegistryInterface
{
    public function add(ClassDefinition $classDefinition): void;

    /**
     * @param class-string $className
     */
    public function get(string $className): ?ClassDefinition;

    /**
     * @param class-string $className
     */
    public function has(string $className): bool;

    /**
     * @return array<class-string, ClassDefinition>
     */
    public function getAll(): array;
}
