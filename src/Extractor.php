<?php

declare(strict_types=1);

namespace Tochka\Hydrator;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Tochka\Hydrator\Contracts\ExtractorInterface;
use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\RootContext;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\ContainerException;
use Tochka\Hydrator\Exceptions\NoTypeHandlerException;
use Tochka\Hydrator\TypeSystem\TypeFromValue;
use Tochka\Hydrator\TypeSystem\TypeInterface;

class Extractor implements ExtractorInterface
{
    private ContainerInterface $container;
    private TypeFromValue $typeFromValue;
    /** @var list<ValueExtractorInterface> */
    private array $extractors = [];

    public function __construct(ContainerInterface $container, TypeFromValue $typeFromValue)
    {
        $this->container = $container;
        $this->typeFromValue = $typeFromValue;
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
            /** @var TExtractor $extractorInstance*/
            $extractorInstance = $this->container->get($extractor);
            $this->extractors[] = $extractorInstance;
        } catch (ContainerExceptionInterface $e) {
            throw new ContainerException(
                sprintf('Error while making [%s]: error binding resolution', $extractor),
                $e
            );
        }
    }

    /**
     * @template TValueType
     * @template TReturnType
     * @param TValueType $value
     * @param TypeInterface<TReturnType> $type
     * @return TReturnType
     */
    public function extract(
        mixed $value,
        TypeInterface $type,
        ?Collection $attributes = null,
        ?Context $context = null
    ): mixed {
        $fromContainer = new FromContainer($value, $this->typeFromValue->inferType($value));
        $toContainer = new ToContainer($type, $attributes ?? new Collection());
        return $this->handle($this->extractors, $fromContainer, $toContainer, $context ?? new RootContext());
    }

    /**
     * @template TValueType
     * @template TReturnType
     * @param list<ValueExtractorInterface> $extractors
     * @param FromContainer<TValueType> $from
     * @param ToContainer<TReturnType> $to
     * @param Context $context
     * @return TReturnType
     */
    private function handle(array $extractors, FromContainer $from, ToContainer $to, Context $context): mixed
    {
        $extractor = array_shift($extractors);

        if ($extractor !== null) {
            return $extractor->extract(
                $from,
                $to,
                $context,
                function (FromContainer $from, ToContainer $to, Context $context) use ($extractors): mixed {
                    return $this->handle($extractors, $from, $to, $context);
                }
            );
        }

        throw new NoTypeHandlerException($context);
    }
}
