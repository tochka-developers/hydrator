<?php

declare(strict_types=1);

namespace Tochka\Hydrator\ExtendedReflection;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Tochka\Hydrator\Exceptions\ContainerException;
use Tochka\Hydrator\ExtendedReflection\TypeFactories\TypeFactoryMiddlewareInterface;
use Tochka\Hydrator\TypeSystem\TypeInterface;
use Tochka\Hydrator\TypeSystem\Types\MixedType;

final class ExtendedTypeFactory
{
    /**
     * @var array<TypeFactoryMiddlewareInterface|class-string<TypeFactoryMiddlewareInterface>>
     */
    private array $typeFactoryMiddleware;
    private ContainerInterface $container;

    /**
     * @param array<TypeFactoryMiddlewareInterface|class-string<TypeFactoryMiddlewareInterface>> $typeFactoryMiddleware
     */
    public function __construct(ContainerInterface $container, array $typeFactoryMiddleware = [])
    {
        $this->typeFactoryMiddleware = $typeFactoryMiddleware;
        $this->container = $container;
    }

    public function getType(ExtendedReflectionInterface $reflector): TypeInterface
    {
        return $this->handle($this->typeFactoryMiddleware, new MixedType(), $reflector);
    }

    /**
     * @param array<TypeFactoryMiddlewareInterface|class-string<TypeFactoryMiddlewareInterface>> $middlewareList
     */
    private function handle(
        array $middlewareList,
        TypeInterface $defaultType,
        ExtendedReflectionInterface $reflector
    ): TypeInterface {
        $currentMiddleware = array_shift($middlewareList);

        if ($currentMiddleware !== null) {
            $middleware = $this->getOrMakeMiddleware($currentMiddleware);
            return $middleware->handle(
                $defaultType,
                $reflector,
                function (
                    TypeInterface $defaultType,
                    ExtendedReflectionInterface $reflector
                ) use ($middlewareList): TypeInterface {
                    return $this->handle($middlewareList, $defaultType, $reflector);
                }
            );
        }

        return $defaultType;
    }

    /**
     * @template T of TypeFactoryMiddlewareInterface
     * @param TypeFactoryMiddlewareInterface|class-string<T> $middleware
     */
    private function getOrMakeMiddleware(
        TypeFactoryMiddlewareInterface|string $middleware
    ): TypeFactoryMiddlewareInterface {
        if ($middleware instanceof TypeFactoryMiddlewareInterface) {
            return $middleware;
        }

        try {
            /** @var T $middlewareInstance */
            $middlewareInstance = $this->container->get($middleware);
        } catch (ContainerExceptionInterface $e) {
            throw new ContainerException(
                sprintf('Error while making [%s]: error binding resolution', $middleware),
                $e
            );
        }

        if (!$middlewareInstance instanceof TypeFactoryMiddlewareInterface) {
            throw new ContainerException(
                sprintf(
                    'Error while making TypeFactoryMiddleware: it must be implement interface [%s]',
                    TypeFactoryMiddlewareInterface::class
                )
            );
        }

        return $middlewareInstance;
    }
}
