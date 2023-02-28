<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\ClassDefinition;

interface ClassDefinitionsRegistryInterface
{
    public function add(ClassDefinition $classDefinition): void;

    /**
     * @param class-string $className
     * @return ClassDefinition|null
     */
    public function get(string $className): ?ClassDefinition;

    /**
     * @param class-string $className
     * @return bool
     */
    public function has(string $className): bool;

    /**
     * @return array<class-string, ClassDefinition>
     */
    public function getAll(): array;
}
