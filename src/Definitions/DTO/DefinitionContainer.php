<?php

namespace Tochka\Hydrator\Definitions\DTO;

/**
 * @template TDefinitionType of DefinitionInterface
 */
class DefinitionContainer
{
    /** @var TDefinitionType */
    private mixed $definition;
    /** @var Collection<ClassDefinition> */
    private Collection $classDefinitions;

    /**
     * @param TDefinitionType $definition
     * @param Collection<ClassDefinition> $classDefinitions
     */
    public function __construct(mixed $definition, Collection $classDefinitions)
    {
        $this->definition = $definition;
        $this->classDefinitions = $classDefinitions;
    }

    /**
     * @return TDefinitionType
     */
    public function getDefinition(): DefinitionInterface
    {
        return $this->definition;
    }

    /**
     * @return Collection<ClassDefinition>
     */
    public function getClassDefinitions(): Collection
    {
        return $this->classDefinitions;
    }
}
