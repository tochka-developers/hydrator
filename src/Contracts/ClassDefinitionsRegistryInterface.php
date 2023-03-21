<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\Definitions\DTO\ClassDefinition;

/**
 * @psalm-api
 */
interface ClassDefinitionsRegistryInterface
{
    public function add(ClassDefinition $classDefinition): void;

    /**
     * @param class-string $className
     */
    public function get(string $className): ?ClassDefinition;

    /**
     * @psalm-assert-if-true ClassDefinition $this->get()
     *
     * @param class-string $className
     */
    public function has(string $className): bool;

    /**
     * @return array<class-string, ClassDefinition>
     */
    public function getAll(): array;
}
