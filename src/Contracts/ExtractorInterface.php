<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\Context;
use Tochka\TypeParser\Collection;
use Tochka\TypeParser\TypeSystem\TypeInterface;

/**
 * @psalm-api
 *
 * @psalm-import-type BeforeHydrateType from ValueExtractorInterface
 */
interface ExtractorInterface
{
    /**
     * @template T of ValueExtractorInterface
     * @param ValueExtractorInterface|class-string<T> $extractor
     * @return void
     */
    public function registerExtractor(ValueExtractorInterface|string $extractor): void;

    /**
     * @template TReturnType
     * @param BeforeHydrateType $value
     * @param TypeInterface<TReturnType> $type
     * @return TReturnType
     */
    public function extract(mixed $value, TypeInterface $type, Collection $attributes, ?Context $context = null): mixed;
}
