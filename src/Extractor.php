<?php

declare(strict_types=1);

namespace Tochka\Hydrator;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Tochka\Hydrator\Contracts\ExtractorInterface;
use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\RootContext;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\ContainerException;
use Tochka\Hydrator\Exceptions\NoTypeHandlerException;
use Tochka\TypeParser\Collection;
use Tochka\TypeParser\TypeSystem\TypeInterface;

/**
 * @psalm-api
 *
 * @psalm-import-type BeforeHydrateType from ValueExtractorInterface
 */
class Extractor implements ExtractorInterface
{
    /** @var list<ValueExtractorInterface> */
    private array $extractors = [];

    public function __construct(
        private readonly ContainerInterface $container
    ) {
    }

    /**
     * @template TExtractor of ValueExtractorInterface
     * @param ValueExtractorInterface|class-string<TExtractor> $extractor
     * @return void
     */
    public function registerExtractor(ValueExtractorInterface|string $extractor): void
    {
        if ($extractor instanceof ValueExtractorInterface) {
            $this->extractors[] = $extractor;
            return;
        }

        try {
            /** @var TExtractor $extractorInstance */
            $extractorInstance = $this->container->get($extractor);
            $this->extractors[] = $extractorInstance;
        } catch (ContainerExceptionInterface $e) {
            throw new ContainerException(
                sprintf('Error while making [%s]: error binding resolution', $extractor),
                $e
            );
        }
    }

    public function extract(
        mixed $value,
        TypeInterface $type,
        ?Collection $attributes = null,
        ?Context $context = null
    ): mixed {
        $toContainer = new ToContainer($type, $attributes ?? new Collection());
        return $this->handle($this->extractors, $value, $toContainer, $context ?? new RootContext());
    }

    /**
     * @template TReturnType
     * @param list<ValueExtractorInterface> $extractors
     * @param BeforeHydrateType $value
     * @param ToContainer<TReturnType> $to
     * @param Context $context
     * @return TReturnType
     */
    private function handle(array $extractors, mixed $value, ToContainer $to, Context $context): mixed
    {
        $extractor = array_shift($extractors);

        if ($extractor !== null) {
            return $extractor->extract(
                $value,
                $to,
                $context,
                function (mixed $value, ToContainer $to, Context $context) use ($extractors): mixed {
                    return $this->handle($extractors, $value, $to, $context);
                }
            );
        }

        throw new NoTypeHandlerException($context);
    }
}
