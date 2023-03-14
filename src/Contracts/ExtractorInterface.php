<?php

namespace Tochka\Hydrator\Contracts;

use Illuminate\Contracts\Container\BindingResolutionException;
use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\TypeSystem\TypeInterface;

interface ExtractorInterface
{
    /**
     * @template T of ValueExtractorInterface
     * @param ValueExtractorInterface|class-string<T> $extractor
     * @return void
     */
    public function registerExtractor(ValueExtractorInterface|string $extractor): void;

    /**
     * @template TValueType
     * @template TReturnType
     * @param TValueType $value
     * @param TypeInterface<TReturnType> $type
     * @return TReturnType
     */
    public function extract(mixed $value, TypeInterface $type, Collection $attributes, ?Context $context = null): mixed;
}
