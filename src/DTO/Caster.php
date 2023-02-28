<?php

namespace Tochka\Hydrator\DTO;

class Caster
{
    /** @var class-string|null */
    private ?string $hydrateCaster = null;
    /** @var class-string|null */
    private ?string $extractCaster = null;
    private TypeDefinition|UnionTypeDefinition|null $typeAfterHydrate = null;
    private TypeDefinition|UnionTypeDefinition|null $typeBeforeExtract = null;

    /**
     * @param class-string $caster
     * @param TypeDefinition|UnionTypeDefinition $type
     * @return void
     */
    public function setHydrateCaster(string $caster, TypeDefinition|UnionTypeDefinition $type): void
    {
        $this->hydrateCaster = $caster;
        $this->typeAfterHydrate = $type;
    }

    /**
     * @param class-string $caster
     * @param TypeDefinition|UnionTypeDefinition $type
     * @return void
     */
    public function setExtractCaster(string $caster, TypeDefinition|UnionTypeDefinition $type): void
    {
        $this->extractCaster = $caster;
        $this->typeBeforeExtract = $type;
    }

    /**
     * @psalm-mutation-free
     * @return class-string|null
     */
    public function getHydrateCaster(): ?string
    {
        return $this->hydrateCaster;
    }

    /**
     * @psalm-mutation-free
     * @return class-string|null
     */
    public function getExtractCaster(): ?string
    {
        return $this->extractCaster;
    }

    /**
     * @psalm-mutation-free
     */
    public function getTypeAfterHydrate(): TypeDefinition|UnionTypeDefinition|null
    {
        return $this->typeAfterHydrate;
    }

    /**
     * @psalm-mutation-free
     */
    public function getTypeBeforeExtract(): TypeDefinition|UnionTypeDefinition|null
    {
        return $this->typeBeforeExtract;
    }
}
