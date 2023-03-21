<?php

declare(strict_types=1);

namespace Tochka\Hydrator;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Tochka\Hydrator\Contracts\HydratorInterface;
use Tochka\Hydrator\Contracts\ValueHydratorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\RootContext;
use Tochka\Hydrator\Exceptions\ContainerException;
use Tochka\Hydrator\Exceptions\NoTypeHandlerException;
use Tochka\TypeParser\Collection;

/**
 * @psalm-api
 *
 * @psalm-import-type AfterHydrateType from ValueHydratorInterface
 */
class Hydrator implements HydratorInterface
{
    /** @var list<ValueHydratorInterface> */
    private array $hydrators = [];

    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    /**
     * @template THydrator of ValueHydratorInterface
     * @param ValueHydratorInterface|class-string<THydrator> $hydrator
     * @return void
     */
    public function registerHydrator(ValueHydratorInterface|string $hydrator): void
    {
        if ($hydrator instanceof ValueHydratorInterface) {
            $this->hydrators[] = $hydrator;
            return;
        }

        try {
            /** @var THydrator $hydratorInstance */
            $hydratorInstance = $this->container->get($hydrator);
            $this->hydrators[] = $hydratorInstance;
        } catch (ContainerExceptionInterface $e) {
            throw new ContainerException(
                sprintf('Error while making [%s]: error binding resolution', $hydrator),
                $e
            );
        }
    }

    public function hydrate(mixed $value, ?Collection $attributes = null, ?Context $context = null): mixed
    {
        return $this->handle(
            $this->hydrators,
            $value,
            $attributes ?? new Collection(),
            $context ?? new RootContext()
        );
    }

    /**
     * @param list<ValueHydratorInterface> $hydrators
     * @return AfterHydrateType
     */
    private function handle(array $hydrators, mixed $value, Collection $attributes, Context $context): mixed
    {
        $hydrator = array_shift($hydrators);

        if ($hydrator !== null) {
            return $hydrator->hydrate(
                $value,
                $attributes,
                $context,
                function (mixed $value, Collection $attributes, Context $context) use ($hydrators): mixed {
                    return $this->handle($hydrators, $value, $attributes, $context);
                }
            );
        }

        throw new NoTypeHandlerException($context);
    }
}
