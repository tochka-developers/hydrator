<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\ExtendedReflection\Reflectors\ExtendedClassReflection;
use Tochka\Hydrator\ExtendedReflection\Reflectors\ExtendedMethodReflection;
use Tochka\Hydrator\ExtendedReflection\Reflectors\ExtendedParameterReflection;
use Tochka\Hydrator\ExtendedReflection\Reflectors\ExtendedPropertyReflection;

/**
 * @psalm-api
 */
interface ExtendedReflectionFactoryInterface
{
    /**
     * @param class-string $className
     * @throws \ReflectionException
     */
    public function makeForClass(string $className): ExtendedClassReflection;

    /**
     * @param class-string $className
     * @throws \ReflectionException
     */
    public function makeForMethod(string $className, string $methodName): ExtendedMethodReflection;

    /**
     * @param class-string $className
     * @throws \ReflectionException
     */
    public function makeForProperty(string $className, string $propertyName): ExtendedPropertyReflection;

    /**
     * @param class-string $className
     * @throws \ReflectionException
     */
    public function makeForParameter(
        string $className,
        string $methodName,
        string $parameterName
    ): ExtendedParameterReflection;
}
