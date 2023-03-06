<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\CastInfo\CastInfoInterface;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\UnionTypeDefinition;

interface CasterRegistryInterface
{
    public function addCaster(HydrateCasterInterface|ExtractCasterInterface $caster): void;

    /**
     * @return class-string|null
     */
    public function getGlobalHydrateCaster(CastInfoInterface $castInfo): ?string;

    /**
     * @return class-string|null
     */
    public function getGlobalExtractCaster(CastInfoInterface $castInfo): ?string;

    /**
     * @param class-string $casterName
     */
    public function getTypeAfterHydrate(
        string $casterName,
        CastInfoInterface $castInfo
    ): TypeDefinition|UnionTypeDefinition;

    /**
     * @param class-string $casterName
     */
    public function getTypeBeforeExtract(
        string $casterName,
        CastInfoInterface $castInfo
    ): TypeDefinition|UnionTypeDefinition;

    /**
     * @param class-string $casterName
     * @param CastInfoInterface $castInfo
     * @param mixed $value
     * @return mixed
     */
    public function extract(string $casterName, CastInfoInterface $castInfo, mixed $value): mixed;

    /**
     * @param class-string $casterName
     * @param CastInfoInterface $castInfo
     * @param mixed $value
     * @return mixed
     */
    public function hydrate(string $casterName, CastInfoInterface $castInfo, mixed $value): mixed;
}
