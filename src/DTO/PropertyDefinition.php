<?php

namespace Tochka\Hydrator\DTO;

use JetBrains\PhpStorm\Pure;

class PropertyDefinition extends ParameterDefinition
{
    private string $className;
    private ?string $hydrateByMethod = null;
    private ?string $extractByMethod = null;

    public function __construct(string $name, string $className, UnionTypeDefinition|TypeDefinition $type)
    {
        parent::__construct($name, $type);

        $this->className = $className;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function getHydrateByMethod(): ?string
    {
        return $this->hydrateByMethod;
    }

    public function setHydrateByMethod(?string $hydrateByMethod): void
    {
        $this->hydrateByMethod = $hydrateByMethod;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function getExtractByMethod(): ?string
    {
        return $this->extractByMethod;
    }

    public function setExtractByMethod(?string $extractByMethod): void
    {
        $this->extractByMethod = $extractByMethod;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function getClassName(): string
    {
        return $this->className;
    }
}
