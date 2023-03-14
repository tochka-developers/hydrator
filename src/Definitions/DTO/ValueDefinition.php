<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Definitions\DTO;

use Tochka\Hydrator\TypeSystem\TypeInterface;

/**
 * @template TValueType
 */
class ValueDefinition
{
    public readonly string $name;
    /** @var TypeInterface<TValueType> */
    public readonly TypeInterface $type;
    public bool $required = false;
    /** @var TValueType|null */
    public mixed $defaultValue = null;
    public bool $hasDefaultValue = false;
    /** @var Collection<object> */
    public Collection $attributes;
    public ?string $description = null;

    /**
     * @param TypeInterface<TValueType> $type
     */
    public function __construct(string $name, TypeInterface $type)
    {
        $this->name = $name;
        $this->type = $type;
        $this->attributes = new Collection();
    }

    public function setDefaultValue(mixed $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
        $this->hasDefaultValue = true;
    }
}
