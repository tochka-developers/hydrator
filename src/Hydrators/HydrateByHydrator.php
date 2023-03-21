<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Hydrators;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Tochka\Hydrator\Attributes\HydrateBy;
use Tochka\Hydrator\Contracts\ValueHydratorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\Exceptions\ContainerValueException;
use Tochka\TypeParser\Collection;

/**
 * @psalm-api
 */
class HydrateByHydrator implements ValueHydratorInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    /**
     * @template THydrator of object
     */
    public function hydrate(mixed $value, Collection $attributes, Context $context, callable $next): mixed
    {
        /**
         * @psalm-ignore-var
         * @var HydrateBy|null $hydrateBy
         */
        $hydrateBy = $attributes->type(HydrateBy::class)->first();

        if ($hydrateBy === null) {
            return $next($value, $attributes, $context);
        }

        /** @var class-string<THydrator>|null $hydrateClassName */
        $hydrateClassName = $hydrateBy->className ?? $context->getClassName();
        $hydrateMethodName = $hydrateBy->methodName;

        if ($hydrateClassName === null) {
            throw new ContainerValueException('Undefined className for hydrate value', $context);
        }

        try {
            /** @var THydrator $hydrator */
            $hydrator = $this->container->get($hydrateClassName);
        } catch (ContainerExceptionInterface $e) {
            throw new ContainerValueException(
                sprintf('Error while make hydrator [%s]', $hydrateClassName),
                $context,
                $e
            );
        }

        if (!method_exists($hydrator, $hydrateMethodName)) {
            throw new ContainerValueException(
                sprintf('Method [%s::%s] for resolve UnionType not found', $hydrateClassName, $hydrateMethodName),
                $context
            );
        }

        return $hydrator->$hydrateMethodName($value, $attributes, $context);
    }
}
