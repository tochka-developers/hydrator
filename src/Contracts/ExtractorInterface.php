<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\ParameterDefinition;
use Tochka\Hydrator\DTO\PropertyDefinition;
use Tochka\Hydrator\DTO\ValueDefinition;

interface ExtractorInterface
{
    /**
     * @param object $parametersToExtract
     * @param class-string $className
     * @param string $methodName
     * @param Context|null $previousContext
     * @return array
     */
    public function extractMethodParameters(
        object $parametersToExtract,
        string $className,
        string $methodName,
        ?Context $previousContext = null
    ): array;

    /**
     * @template TExtractedObject
     * @param object $objectToExtract
     * @param class-string $className
     * @param Context|null $previousContext
     * @return TExtractedObject
     */
    public function extractObject(object $objectToExtract, string $className, ?Context $previousContext = null): object;

    public function extractProperty(
        mixed $propertyToExtract,
        PropertyDefinition $propertyDefinition,
        object $extractedObject,
        ?Context $previousContext = null
    ): mixed;

    public function extractParameter(
        mixed $parameterToExtract,
        ParameterDefinition $parameterDefinition,
        ?Context $previousContext = null
    ): mixed;

    public function extractValue(
        mixed $valueToExtract,
        ValueDefinition $valueDefinition,
        ?Context $previousContext = null
    ): mixed;
}
