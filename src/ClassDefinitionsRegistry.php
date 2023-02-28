<?php

namespace Tochka\Hydrator;

use Tochka\Hydrator\Contracts\ClassDefinitionsRegistryInterface;
use Tochka\Hydrator\DTO\ClassDefinition;

class ClassDefinitionsRegistry implements ClassDefinitionsRegistryInterface
{
    /**
     * @var array<class-string, ClassDefinition>
     */
    private array $classDefinitions = [];

    public function add(ClassDefinition $classDefinition): void
    {
        $this->classDefinitions[$classDefinition->getClassName()] = $classDefinition;
    }

    public function get(string $className): ?ClassDefinition
    {
        return $this->classDefinitions[$className] ?? null;
    }

    public function has(string $className): bool
    {
        return array_key_exists($className, $this->classDefinitions);
    }

    public function getAll(): array
    {
        return $this->classDefinitions;
    }
}
