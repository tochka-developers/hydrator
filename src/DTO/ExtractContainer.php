<?php

namespace Tochka\Hydrator\DTO;

use JetBrains\PhpStorm\Pure;
use Tochka\Hydrator\Contracts\ExtractorInterface;

class ExtractContainer
{
    private ExtractorInterface $extractor;
    private mixed $valueToExtract;
    private TypeDefinition $type;

    public function __construct(ExtractorInterface $extractor, mixed $valueToExtract, TypeDefinition $type)
    {
        $this->extractor = $extractor;
        $this->valueToExtract = $valueToExtract;
        $this->type = $type;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function getExtractor(): ExtractorInterface
    {
        return $this->extractor;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function getValueToExtract(): mixed
    {
        return $this->valueToExtract;
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
