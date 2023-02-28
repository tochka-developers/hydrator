<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\ParameterDefinition;
use Tochka\Hydrator\DTO\PropertyDefinition;
use Tochka\Hydrator\DTO\ValueDefinition;

interface ExtractorInterface
{
    /**
     * @param object $parametersToExtract
     * @param class-string $className
     * @param string $methodName
     * @return array
     */
    public function extractMethodParameters(object $parametersToExtract, string $className, string $methodName): array;

    /**
     * @param object $objectToExtract
     * @param class-string $className
     * @return object
     */
    public function extractObject(object $objectToExtract, string $className): object;

    public function extractProperty(
        mixed $propertyToExtract,
        PropertyDefinition $propertyReference,
        object $extractedObject
    ): mixed;

    public function extractParameter(mixed $parameterToExtract, ParameterDefinition $parameterDefinition): mixed;

    public function extractValue(mixed $valueToExtract, ValueDefinition $valueDefinition): mixed;
}
