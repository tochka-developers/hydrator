<?php

namespace Tochka\Hydrator\DTO;

class UnionTypeDefinition implements CallableTypeInterface, NullableTypeInterface, NeedResolveTypeInterface
{
    /** @var array<TypeDefinition> */
    private array $types;
    private bool $nullable = false;

    /**
     * @param array<TypeDefinition> $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * @return array<TypeDefinition>
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    public function call(callable $callback): void
    {
        foreach ($this->types as $type) {
            $callback($type);
        }
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function setNullable(bool $nullable): void
    {
        $this->nullable = $nullable;
    }

    public function needResolve(): bool
    {
        return true;
    }

    public function setNeedResolve(bool $needResolve): void
    {
    }
}
