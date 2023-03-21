<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Tochka\Hydrator\Attributes\ResolveBy;
use Tochka\Hydrator\Contracts\ExtractorInterface;
use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\BaseTransformingException;
use Tochka\Hydrator\Exceptions\ContainerValueException;
use Tochka\Hydrator\Exceptions\UnionTypeResolveException;
use Tochka\TypeParser\Collection;
use Tochka\TypeParser\TypeSystem\TypeInterface;
use Tochka\TypeParser\TypeSystem\Types\StringType;
use Tochka\TypeParser\TypeSystem\Types\UnionType;

/**
 * @psalm-api
 */
final class UnionExtractor implements ValueExtractorInterface
{
    public function __construct(
        private readonly ExtractorInterface $extractor,
        private readonly ContainerInterface $container,
    ) {
    }

    public function extract(mixed $value, ToContainer $to, Context $context, callable $next): mixed
    {
        /** @psalm-suppress RedundantCondition */
        if (!$to->type instanceof UnionType) {
            return $next($value, $to, $context);
        }

        $sortableTypes = $to->type->types->sort($this->sortTypes(...));

        /**
         * @psalm-ignore-var
         * @var ResolveBy|null $resolveBy
         */
        $resolveBy = $to->attributes->type(ResolveBy::class)->first();
        if ($resolveBy !== null) {
            $resolvedType = $this->resolveType(
                $resolveBy->className ?? $context->getClassName(),
                $resolveBy->methodName,
                $sortableTypes,
                $value,
                $context
            );

            if ($resolvedType === null) {
                throw new UnionTypeResolveException([], $context);
            }

            return $this->extractor->extract($value, $resolvedType, $to->attributes, $context);
        }

        // попробуем разрешить базовые типы
        $errors = [];
        foreach ($sortableTypes as $type) {
            try {
                return $this->extractor->extract($value, $type, $to->attributes, $context);
            } catch (BaseTransformingException $e) {
                $errors[] = $e;
            }
        }

        throw new UnionTypeResolveException($errors, $context);
    }

    /**
     * @template TResolver of object
     * @param class-string<TResolver>|null $resolveClassName
     * @param Collection<TypeInterface> $types
     */
    private function resolveType(
        ?string $resolveClassName,
        string $resolveMethodName,
        Collection $types,
        mixed $value,
        Context $context
    ): ?TypeInterface {
        if ($resolveClassName === null) {
            throw new ContainerValueException('Undefined className for resolve UnionType', $context);
        }

        try {
            /** @var TResolver $resolver */
            $resolver = $this->container->get($resolveClassName);
        } catch (ContainerExceptionInterface $e) {
            throw new ContainerValueException(
                sprintf('Error while make UnionType resolver [%s]', $resolveClassName),
                $context,
                $e
            );
        }

        if (!method_exists($resolver, $resolveMethodName)) {
            throw new ContainerValueException(
                sprintf('Method [%s::%s] for resolve UnionType not found', $resolveClassName, $resolveMethodName),
                $context
            );
        }

        foreach ($types as $type) {
            if ($resolver->$resolveMethodName($value, $type)) {
                return $type;
            }
        }

        return null;
    }

    private function sortTypes(TypeInterface $a, TypeInterface $b): int
    {
        if ($a instanceof StringType) {
            return 1;
        }
        if ($b instanceof StringType) {
            return -1;
        }

        return 0;
    }
}
