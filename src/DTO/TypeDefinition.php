<?php

namespace Tochka\Hydrator\DTO;

class TypeDefinition implements CallableTypeInterface, NullableTypeInterface, NeedResolveTypeInterface
{
    private ScalarTypeEnum $scalarType;
    private TypeDefinition|UnionTypeDefinition|null $valueType = null;
    /** @var class-string|null */
    private ?string $className = null;
    private Caster $caster;
    private bool $nullable = false;
    private bool $needResolve = false;

    public function __construct(ScalarTypeEnum $type)
    {
        $this->scalarType = $type;
        $this->caster = new Caster();
    }

    public function call(callable $callback): void
    {
        $callback($this);
    }

    /**
     * @psalm-mutation-free
     */
    public function getScalarType(): ScalarTypeEnum
    {
        return $this->scalarType;
    }

    public function setScalarType(ScalarTypeEnum $scalarType): void
    {
        $this->scalarType = $scalarType;
    }

    /**
     * @psalm-mutation-free
     */
    public function getValueType(): TypeDefinition|UnionTypeDefinition|null
    {
        return $this->valueType;
    }

    public function setValueType(TypeDefinition|UnionTypeDefinition $valueType): void
    {
        $this->valueType = $valueType;
    }

    /**
     * @psalm-mutation-free
     * @return class-string|null
     */
    public function getClassName(): ?string
    {
        return $this->className;
    }

    /**
     * @param class-string $className
     * @return void
     */
    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    /**
     * @psalm-mutation-free
     */
    public function getCaster(): Caster
    {
        return $this->caster;
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
        return $this->needResolve;
    }

    public function setNeedResolve(bool $needResolve): void
    {
        $this->needResolve = $needResolve;
    }
}
