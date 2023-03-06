<?php

namespace Tochka\Hydrator\ExtendedReflection;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tochka\Hydrator\Exceptions\TypeFactoryException;
use Tochka\Hydrator\ExtendedReflection\TypeFactories\TypeFactoryMiddlewareInterface;
use Tochka\Hydrator\TypeSystem\TypeInterface;
use Tochka\Hydrator\TypeSystem\Types\MixedType;

final class ExtendedTypeFactory
{
    /**
     * @var array<TypeFactoryMiddlewareInterface|class-string<TypeFactoryMiddlewareInterface>>
     */
    private array $typeFactoryMiddleware;
    private Container $container;

    /**
     * @param array<TypeFactoryMiddlewareInterface|class-string<TypeFactoryMiddlewareInterface>> $typeFactoryMiddleware
     */
    public function __construct(Container $container, array $typeFactoryMiddleware = [])
    {
        $this->typeFactoryMiddleware = $typeFactoryMiddleware;
        $this->container = $container;
    }

    public function getType(ExtendedReflectionWithTypeInterface $reflector): TypeInterface
    {
        return $this->handle($this->typeFactoryMiddleware, new MixedType(), $reflector);
    }

    /**
     * @param array<TypeFactoryMiddlewareInterface|class-string<TypeFactoryMiddlewareInterface>> $middlewareList
     */
    private function handle(
        array $middlewareList,
        TypeInterface $defaultType,
        ExtendedReflectionWithTypeInterface $reflector
    ): TypeInterface {
        $currentMiddleware = array_shift($middlewareList);

        if ($currentMiddleware !== null) {
            $middleware = $this->getOrMakeMiddleware($currentMiddleware);
            return $middleware->handle(
                $defaultType,
                $reflector,
                function (
                    TypeInterface $defaultType,
                    ExtendedReflectionWithTypeInterface $reflector
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
        if (is_string($middleware)) {
            try {
                /** @var T $middleware */
                $middleware = $this->container->make($middleware);
            } catch (BindingResolutionException $e) {
                throw new TypeFactoryException(
                    'Error while making TypeFactoryMiddleware: error binding resolution',
                    $e
                );
            }
        }

        if ($middleware instanceof TypeFactoryMiddlewareInterface) {
            return $middleware;
        }

        throw new TypeFactoryException(
            sprintf(
                'Error while making TypeFactoryMiddleware: it must be implement interface [%s]',
                TypeFactoryMiddlewareInterface::class
            )
        );
    }
}
