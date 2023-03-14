<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Definitions\DTO;

use Tochka\Hydrator\TypeSystem\Types\MixedType;

class MethodDefinition implements DefinitionInterface
{
    /** @var class-string */
    public readonly string $className;
    public readonly string $methodName;
    /** @var Collection<ValueDefinition> */
    public Collection $parameters;
    public ReturnDefinition $returnDefinition;
    /** @var Collection<object> */
    public Collection $attributes;
    public ?string $description = null;

    /**
     * @param class-string $className
     */
    public function __construct(string $className, string $methodName)
    {
        $this->className = $className;
        $this->methodName = $methodName;
        /** @var list<ValueDefinition> $parameters */
        $parameters = [];
        $this->parameters = new Collection($parameters);
        $this->attributes = new Collection([]);
        $this->returnDefinition = new ReturnDefinition(new MixedType());
    }
}
