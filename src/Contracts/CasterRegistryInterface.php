<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\CastInfo;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\UnionTypeDefinition;

interface CasterRegistryInterface
{
    public function addCaster(HydrateCasterInterface|ExtractCasterInterface $caster): void;

    /**
     * @return class-string|null
     */
    public function getGlobalHydrateCaster(CastInfo $castInfo): ?string;

    /**
     * @return class-string|null
     */
    public function getGlobalExtractCaster(CastInfo $castInfo): ?string;

    /**
     * @param class-string $casterName
     */
    public function getTypeAfterHydrate(string $casterName, CastInfo $castInfo): TypeDefinition|UnionTypeDefinition;

    /**
     * @param class-string $casterName
     */
    public function getTypeBeforeExtract(string $casterName, CastInfo $castInfo): TypeDefinition|UnionTypeDefinition;

    /**
     * @param class-string $casterName
     * @param CastInfo $castInfo
     * @param mixed $value
     * @return mixed
     */
    public function extract(string $casterName, CastInfo $castInfo, mixed $value): mixed;

    /**
     * @param class-string $casterName
     * @param CastInfo $castInfo
     * @param mixed $value
     * @return mixed
     */
    public function hydrate(string $casterName, CastInfo $castInfo, mixed $value): mixed;
}
