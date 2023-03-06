<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\ParameterDefinition;
use Tochka\Hydrator\DTO\PropertyDefinition;
use Tochka\Hydrator\DTO\ValueDefinition;

interface HydratorInterface
{
    /**
     * @param array $parametersToHydrate
     * @param class-string $className
     * @param string $methodName
     * @return array
     */
    public function hydrateMethodParameters(array $parametersToHydrate, string $className, string $methodName): array;

    /**
     * @param object $objectToHydrate
     * @param class-string $className
     * @return object
     */
    public function hydrateObject(object $objectToHydrate, string $className): object;

    public function hydrateProperty(
        mixed $propertyToHydrate,
        PropertyDefinition $propertyDefinition,
        object $hydratedObject
    ): mixed;

    public function hydrateParameter(mixed $parameterToHydrate, ParameterDefinition $parameterDefinition): mixed;

    public function hydrateValue(mixed $valueToHydrate, ValueDefinition $valueDefinition): mixed;
}
