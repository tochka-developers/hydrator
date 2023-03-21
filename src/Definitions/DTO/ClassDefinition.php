<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Definitions\DTO;

use Tochka\TypeParser\Collection;

/**
 * @psalm-api
 *
 * @template TClass of object
 */
final class ClassDefinition implements DefinitionInterface
{
    /** @var class-string<TClass> */
    public readonly string $className;
    /** @var Collection<ValueDefinition> */
    public Collection $properties;
    /** @var Collection<object> */
    public Collection $attributes;
    public ?string $summary = null;
    public ?string $description = null;
    public bool $virtual = false;
    public bool $isInterface = false;
    public bool $isEnum = false;
    public bool $isTrait = false;

    /**
     * @param class-string<TClass> $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
        /** @var Collection<ValueDefinition> */
        $this->properties = new Collection();
        $this->attributes = new Collection();
    }
}
