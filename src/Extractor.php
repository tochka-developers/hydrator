<?php

namespace Tochka\Hydrator;

use Illuminate\Container\Container;
use Illuminate\Pipeline\Pipeline;
use Tochka\Hydrator\Contracts\CasterRegistryInterface;
use Tochka\Hydrator\Contracts\DefinitionParserInterface;
use Tochka\Hydrator\Contracts\ExtractorInterface;
use Tochka\Hydrator\Contracts\TypeResolverInterface;
use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\CastInfo\CastInfoForClass;
use Tochka\Hydrator\DTO\CastInfo\CastInfoForType;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\ExtractContainer;
use Tochka\Hydrator\DTO\ParameterDefinition;
use Tochka\Hydrator\DTO\PropertyDefinition;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\UnionTypeDefinition;
use Tochka\Hydrator\DTO\ValueDefinition;
use Tochka\Hydrator\Exceptions\ExtractException;

class Extractor implements ExtractorInterface
{
    private DefinitionParserInterface $definitionParser;
    private CasterRegistryInterface $casterRegistry;
    private Pipeline $pipeline;
    /** @var array<ValueExtractorInterface> */
    private array $valueExtractors = [];
    private TypeResolverInterface $typeResolver;

    public function __construct(
        Container $container,
        DefinitionParserInterface $definitionParser,
        CasterRegistryInterface $casterRegistry,
        TypeResolverInterface $typeResolver,
    ) {
        $this->definitionParser = $definitionParser;
        $this->casterRegistry = $casterRegistry;
        $this->typeResolver = $typeResolver;
        $this->pipeline = new Pipeline($container);
    }

    public function registerValueExtractor(ValueExtractorInterface $valueExtractor): void
    {
        $this->valueExtractors[] = $valueExtractor;
    }

    public function extractMethodParameters(
        object $parametersToExtract,
        string $className,
        string $methodName,
        ?Context $previousContext = null
    ): array {
        $parameterDefinitions = $this->definitionParser->getMethodParameters($className, $methodName);

        /** @var array<string, mixed> $extractedParameters */
        $extractedParameters = [];

        foreach ($parameterDefinitions as $parameterDefinition) {


            try {
                $parameterName = $parameterDefinition->getName();

                if (!property_exists($parametersToExtract, $parameterName)) {
                    if ($parameterDefinition->isRequired() && !$parameterDefinition->hasDefaultValue()) {
                        // TODO: exception
                        throw new \RuntimeException();
                    }

                    $extractedParameters[$parameterName] = $parameterDefinition->getDefaultValue();

                    continue;
                }

                $extractedParameters[$parameterName] = $this->extractParameter(
                    $parametersToExtract->$parameterName,
                    $parameterDefinition,
                    $previousContext
                );
            } catch (ExtractException $e) {
                $context = new Context($parameterDefinition->getName(), $previousContext);

            }
        }

        return $extractedParameters;
    }

    /**
     * @template TExtractedObject
     * @param object $objectToExtract
     * @param class-string $className
     * @param Context|null $previousContext
     * @return TExtractedObject
     * @throws \ReflectionException
     */
    public function extractObject(object $objectToExtract, string $className, ?Context $previousContext = null): object
    {
        $context = Context::forClass($className, $previousContext);

        $classDefinition = $this->definitionParser->getClassDefinition($className);

        if ($classDefinition->getCaster()->getExtractCaster() !== null) {
            $castInfo = new CastInfoForClass($classDefinition);
            /** @var TExtractedObject */
            return $this->casterRegistry->extract(
                $classDefinition->getCaster()->getExtractCaster(),
                $castInfo,
                $objectToExtract
            );
        }

        $reflectionClass = new \ReflectionClass($className);
        $extractedObject = $reflectionClass->newInstanceWithoutConstructor();

        foreach ($classDefinition->getProperties() as $propertyReference) {
            $propertyName = $propertyReference->getName();

            if (!property_exists($objectToExtract, $propertyName)) {
                if ($propertyReference->isRequired() && !$propertyReference->hasDefaultValue()) {
                    // TODO: exception
                    throw new \RuntimeException();
                }

                $extractedObject->$propertyName = $propertyReference->getDefaultValue();

                continue;
            }

            $extractedObject->$propertyName = $this->extractProperty(
                $objectToExtract->$propertyName,
                $propertyReference,
                $extractedObject
            );
        }

        return $extractedObject;
    }

    public function extractProperty(
        mixed $propertyToExtract,
        PropertyDefinition $propertyDefinition,
        object $extractedObject,
        ?Context $previousContext = null
    ): mixed {
        $context = Context::forProperty($propertyDefinition->getClassName(), $propertyDefinition->getName(), $previousContext);

        if ($propertyToExtract === null && !$propertyDefinition->getType()->isNullable()) {
            // TODO: exception
            throw new \RuntimeException();
        }

        $castInfo = new CastInfoForType($propertyDefinition->getType(), $propertyDefinition->getAttributes());

        $extractByMethod = $propertyDefinition->getExtractByMethod();
        if ($extractByMethod !== null) {
            if (method_exists($extractedObject, $extractByMethod)) {
                return $extractedObject->$extractByMethod($castInfo, $propertyToExtract);
            } else {
                // TODO: exception
                throw new \RuntimeException();
            }
        }

        return $this->extractParameter($propertyToExtract, $propertyDefinition);
    }

    public function extractParameter(mixed $parameterToExtract, ParameterDefinition $parameterDefinition, ?Context $previousContext = null): mixed
    {
        $context = Context::forParameter(
            $parameterDefinition->getClassName(),
            $parameterDefinition->getMethodName(),
            $parameterDefinition->getName(),
            $previousContext
        );

        if ($parameterToExtract === null && !$parameterDefinition->getType()->isNullable()) {
            // TODO: exception
            throw new \RuntimeException();
        }

        if ($parameterDefinition->getCaster()->getExtractCaster() !== null) {
            $castInfo = new CastInfoForType($parameterDefinition->getType(), $parameterDefinition->getAttributes());

            return $this->casterRegistry->extract(
                $parameterDefinition->getCaster()->getExtractCaster(),
                $castInfo,
                $parameterToExtract
            );
        }

        return $this->extractValue($parameterToExtract, $parameterDefinition);
    }

    public function extractValue(mixed $valueToExtract, ValueDefinition $valueDefinition, ?Context $previousContext = null): mixed
    {
        if ($valueToExtract === null && !$valueDefinition->getType()->isNullable()) {
            // TODO: exception
            throw new \RuntimeException();
        }

        $type = $valueDefinition->getType();

        if ($type instanceof UnionTypeDefinition || $type->needResolve()) {
            $type = $this->typeResolver->resolve($valueToExtract, $type);

            if ($type === null) {
                // TODO: exception
                throw new \RuntimeException();
            }
        }

        return $this->extractValueToType($valueToExtract, $type);
    }

    private function extractValueToType(mixed $valueToExtract, TypeDefinition $typeReference): mixed
    {
        $container = new ExtractContainer($this, $valueToExtract, $typeReference);
        return $this->pipeline->send($container)
            ->through($this->valueExtractors)
            ->via('extract')
            ->then(function () {
                // TODO: exception
                throw new \RuntimeException();
            });
    }
}
