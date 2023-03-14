<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Definitions\DTO;

final class ClassDefinition implements DefinitionInterface
{
    /** @var class-string */
    public readonly string $className;
    /** @var Collection<ValueDefinition> */
    public Collection $properties;
    /** @var Collection<object> */
    public Collection $attributes;
    public ?string $description = null;
    public bool $virtual = false;
    public bool $isInterface = false;
    public bool $isEnum = false;
    public bool $isTrait = false;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
        $this->properties = new Collection([]);
        $this->attributes = new Collection([]);
    }
}
