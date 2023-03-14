<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Tochka\Hydrator\Attributes\ExtractBy;
use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\ContainerValueException;

final class ExtractByExtractor implements ValueExtractorInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    /**
     * @template TExtractor of object
     */
    public function extract(FromContainer $from, ToContainer $to, Context $context, callable $next): mixed
    {
        /**
         * @psalm-ignore-var
         * @var ExtractBy|null $extractBy
         */
        $extractBy = $to->attributes->type(ExtractBy::class)->first();

        if ($extractBy === null) {
            return $next($from, $to, $context);
        }

        /** @var class-string<TExtractor>|null $extractClassName */
        $extractClassName = $extractBy->className ?? $context->getClassName();
        $extractMethodName = $extractBy->methodName;

        if ($extractClassName === null) {
            throw new ContainerValueException('Undefined className for extract value', $context);
        }

        try {
            /** @var TExtractor $extractor */
            $extractor = $this->container->get($extractClassName);
        } catch (ContainerExceptionInterface $e) {
            throw new ContainerValueException(
                sprintf('Error while make extractor [%s]', $extractClassName),
                $context,
                $e
            );
        }

        if (!method_exists($extractor, $extractMethodName)) {
            throw new ContainerValueException(
                sprintf('Method [%s::%s] for resolve UnionType not found', $extractClassName, $extractMethodName),
                $context
            );
        }

        return $extractor->$extractMethodName($from, $to, $context);
    }
}
