<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\Support\ExtendedReflection;

/**
 * @psalm-api
 */
interface ExtendedReflectionFactoryInterface
{
    public function make(\Reflector $reflector): ExtendedReflection;

    /**
     * @param class-string $className
     * @return ExtendedReflection
     */
    public function makeForClass(string $className): ExtendedReflection;

    /**
     * @param class-string $className
     * @param string $methodName
     * @return ExtendedReflection
     */
    public function makeForMethod(string $className, string $methodName): ExtendedReflection;

    /**
     * @param class-string $className
     * @param string $propertyName
     * @return ExtendedReflection
     */
    public function makeForProperty(string $className, string $propertyName): ExtendedReflection;

    /**
     * @param class-string $className
     * @param string $methodName
     * @param string $parameterName
     * @return ExtendedReflection
     */
    public function makeForParameter(string $className, string $methodName, string $parameterName): ExtendedReflection;
}
