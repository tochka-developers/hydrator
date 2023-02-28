<?php

namespace Tochka\Hydrator;

use Tochka\Hydrator\Contracts\DefinitionParserInterface;
use Tochka\Hydrator\DTO\ValueDefinition;

class Hydrator
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

    public function hydrateObject(object $objectForHydrate): object
    {
    }

    public function hydrateValue(mixed $valueForHydrate, ValueDefinition $valueReference): mixed
    {
    }
}
