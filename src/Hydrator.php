<?php

declare(strict_types=1);

namespace Tochka\Hydrator;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tochka\Hydrator\Contracts\HydratorInterface;
use Tochka\Hydrator\Contracts\ValueHydratorInterface;
use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\NoTypeHandlerException;
use Tochka\Hydrator\TypeSystem\TypeFromValue;
use Tochka\Hydrator\TypeSystem\TypeInterface;

class Hydrator implements HydratorInterface
{
    private Container $container;
    private TypeFromValue $typeFromValue;
    /** @var list<ValueHydratorInterface> */
    private array $hydrators = [];

    public function __construct(Container $container, TypeFromValue $typeFromValue)
    {
        $this->container = $container;
        $this->typeFromValue = $typeFromValue;
    }

    /**
     * @param ValueHydratorInterface|class-string<ValueHydratorInterface> $hydrator
     * @return void
     * @throws BindingResolutionException
     */
    public function registerHydrator(ValueHydratorInterface|string $hydrator): void
    {
        if (is_string($hydrator)) {
            $this->hydrators[] = $this->container->make($hydrator);
        } else {
            $this->hydrators[] = $hydrator;
        }
    }

    /**
     * @template T
     * @param mixed $value
     * @param TypeInterface<T> $type
     * @return T
     */
    public function hydrate(
        mixed $value,
        TypeInterface $type,
        ?Collection $attributes = null,
        ?Context $context = null
    ): mixed {
        $fromContainer = new FromContainer($value, $this->typeFromValue->inferType($value));
        $toContainer = new ToContainer($type, $attributes ?? new Collection([]));
        return $this->handle($this->hydrators, $fromContainer, $toContainer, $context);
    }

    /**
     * @param list<ValueHydratorInterface> $hydrators
     */
    private function handle(array $hydrators, FromContainer $from, ToContainer $to, ?Context $context): mixed
    {
        $hydrator = array_shift($hydrators);

        if ($hydrator !== null) {
            return $hydrator->hydrate(
                $from,
                $to,
                $context,
                function (FromContainer $from, ToContainer $to, ?Context $context) use ($hydrators): mixed {
                    return $this->handle($hydrators, $from, $to, $context);
                }
            );
        }

        throw new NoTypeHandlerException($context);
    }
}
