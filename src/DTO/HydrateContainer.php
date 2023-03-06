<?php

namespace Tochka\Hydrator\DTO;

use JetBrains\PhpStorm\Pure;
use Tochka\Hydrator\Contracts\HydratorInterface;

class HydrateContainer
{
    private HydratorInterface $hydrator;
    private mixed $valueToHydrate;
    private TypeDefinition $type;

    public function __construct(HydratorInterface $extractor, mixed $valueToHydrate, TypeDefinition $type)
    {
        $this->hydrator = $extractor;
        $this->valueToHydrate = $valueToHydrate;
        $this->type = $type;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function getHydrator(): HydratorInterface
    {
        return $this->hydrator;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function getValueToHydrate(): mixed
    {
        return $this->valueToHydrate;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function getType(): TypeDefinition
    {
        return $this->type;
    }
}
