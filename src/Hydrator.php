<?php

namespace Tochka\Hydrator;

use Tochka\Hydrator\Contracts\DefinitionParserInterface;
use Tochka\Hydrator\Contracts\HydratorInterface;
use Tochka\Hydrator\DTO\ParameterDefinition;
use Tochka\Hydrator\DTO\PropertyDefinition;
use Tochka\Hydrator\DTO\ValueDefinition;

class Hydrator implements HydratorInterface
{
    private DefinitionParserInterface $parametersParser;

    public function __construct(DefinitionParserInterface $parametersParser)
    {
        $this->parametersParser = $parametersParser;
    }

    public function hydrateMethodParameters(array $parametersForHydrate, string $className, string $methodName): array
    {
        $parametersReference = $this->parametersParser->getMethodParameters($className, $methodName);

        foreach ($parametersReference as $parameterReference) {
        }
    }

    public function hydrateObject(object $objectToHydrate, string $className): object
    {
        // TODO: Implement hydrateObject() method.
    }

    public function hydrateProperty(
        mixed $propertyToHydrate,
        PropertyDefinition $propertyDefinition,
        object $hydratedObject
    ): mixed {
        // TODO: Implement hydrateProperty() method.
    }

    public function hydrateParameter(mixed $parameterToHydrate, ParameterDefinition $parameterDefinition): mixed
    {
        // TODO: Implement hydrateParameter() method.
    }

    public function hydrateValue(mixed $valueToHydrate, ValueDefinition $valueDefinition): mixed
    {
        // TODO: Implement hydrateValue() method.
    }
}
