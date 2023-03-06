<?php

namespace Tochka\Hydrator\Support;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tochka\Hydrator\Contracts\CasterRegistryInterface;
use Tochka\Hydrator\Contracts\ExtractCasterInterface;
use Tochka\Hydrator\Contracts\HydrateCasterInterface;
use Tochka\Hydrator\DTO\CastInfo\CastInfoInterface;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\UnionTypeDefinition;
use Tochka\Hydrator\Exceptions\CasterImplementsException;
use Tochka\Hydrator\Exceptions\MakeCasterException;

class CasterRegistry implements CasterRegistryInterface
{
    /** @var array<class-string, ExtractCasterInterface> */
    private array $globalExtractCasters = [];
    /** @var array<class-string, HydrateCasterInterface> */
    private array $globalHydrateCasters = [];
    /** @var array<class-string, ExtractCasterInterface> */
    private array $extractCasters = [];
    /** @var array<class-string, HydrateCasterInterface> */
    private array $hydrateCasters = [];

    private Container $container;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function addCaster(HydrateCasterInterface|ExtractCasterInterface $caster): void
    {
        if ($caster instanceof HydrateCasterInterface) {
            $this->globalHydrateCasters[$caster::class] = $caster;
        }
        if ($caster instanceof ExtractCasterInterface) {
            $this->globalExtractCasters[$caster::class] = $caster;
        }
    }

    public function getGlobalHydrateCaster(CastInfoInterface $castInfo): ?string
    {
        foreach ($this->globalHydrateCasters as $casterName => $caster) {
            if ($caster->canHydrate($castInfo)) {
                return $casterName;
            }
        }

        return null;
    }

    public function getGlobalExtractCaster(CastInfoInterface $castInfo): ?string
    {
        foreach ($this->globalExtractCasters as $casterName => $caster) {
            if ($caster->canExtract($castInfo)) {
                return $casterName;
            }
        }

        return null;
    }

    public function getTypeAfterHydrate(
        string $casterName,
        CastInfoInterface $castInfo
    ): TypeDefinition|UnionTypeDefinition {
        if (array_key_exists($casterName, $this->globalHydrateCasters)) {
            return $this->globalHydrateCasters[$casterName]->typeAfterHydrate($castInfo);
        }

        if (!array_key_exists($casterName, $this->hydrateCasters)) {
            $this->hydrateCasters[$casterName] = $this->makeCaster($casterName, HydrateCasterInterface::class);
        }

        return $this->hydrateCasters[$casterName]->typeAfterHydrate($castInfo);
    }

    public function getTypeBeforeExtract(
        string $casterName,
        CastInfoInterface $castInfo
    ): TypeDefinition|UnionTypeDefinition {
        if (array_key_exists($casterName, $this->globalExtractCasters)) {
            return $this->globalExtractCasters[$casterName]->typeBeforeExtract($castInfo);
        }

        if (!array_key_exists($casterName, $this->extractCasters)) {
            $this->extractCasters[$casterName] = $this->makeCaster($casterName, ExtractCasterInterface::class);
        }

        return $this->extractCasters[$casterName]->typeBeforeExtract($castInfo);
    }

    public function extract(string $casterName, CastInfoInterface $castInfo, mixed $value): mixed
    {
        if (array_key_exists($casterName, $this->globalExtractCasters)) {
            return $this->globalExtractCasters[$casterName]->extract($castInfo, $value);
        }

        if (!array_key_exists($casterName, $this->extractCasters)) {
            $this->extractCasters[$casterName] = $this->makeCaster($casterName, ExtractCasterInterface::class);
        }

        return $this->extractCasters[$casterName]->extract($castInfo, $value);
    }

    public function hydrate(string $casterName, CastInfoInterface $castInfo, mixed $value): mixed
    {
        if (array_key_exists($casterName, $this->globalHydrateCasters)) {
            return $this->globalHydrateCasters[$casterName]->hydrate($castInfo, $value);
        }

        if (!array_key_exists($casterName, $this->hydrateCasters)) {
            $this->hydrateCasters[$casterName] = $this->makeCaster($casterName, HydrateCasterInterface::class);
        }

        return $this->hydrateCasters[$casterName]->hydrate($castInfo, $value);
    }

    /**
     * @template TCasterInterface
     * @param string $casterName
     * @param class-string<TCasterInterface> $interface
     * @return TCasterInterface
     */
    private function makeCaster(string $casterName, string $interface): object
    {
        try {
            $caster = $this->container->make($casterName);
        } catch (BindingResolutionException $e) {
            throw new MakeCasterException(previous: $e);
        }

        if (!$caster instanceof $interface) {
            throw new CasterImplementsException($casterName, $interface);
        }

        return $caster;
    }
}
