<?php

declare(strict_types=1);

namespace Tochka\Hydrator;

use Tochka\Hydrator\Attributes\Alias;
use Tochka\Hydrator\Contracts\ClassDefinitionParserInterface;
use Tochka\Hydrator\Contracts\ExtractorInterface;
use Tochka\Hydrator\Contracts\MethodDefinitionParserInterface;
use Tochka\Hydrator\Definitions\DTO\ValueDefinition;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\RootContext;
use Tochka\Hydrator\Exceptions\BaseTransformingException;
use Tochka\Hydrator\Exceptions\NoDefaultValueException;
use Tochka\Hydrator\Exceptions\NotPresentRequiredValueException;
use Tochka\Hydrator\Exceptions\SameTransformingFieldException;
use Tochka\TypeParser\TypeSystem\Types\NamedObjectType;

/**
 * @psalm-api
 */
class ExtractFactory
{
    public function __construct(
        private readonly ClassDefinitionParserInterface $classDefinitionParser,
        private readonly MethodDefinitionParserInterface $methodDefinitionParser,
        private readonly ExtractorInterface $extractor,
    ) {
    }

    /**
     * @template TObject
     * @param class-string<TObject> $className
     * @return TObject
     */
    public function extractToObject(mixed $value, string $className, ?Context $context = null): object
    {
        $classDefinition = $this->classDefinitionParser->getDefinition($className);

        return $this->extractor->extract(
            $value,
            new NamedObjectType($className),
            $classDefinition->attributes,
            $context
        );
    }

    /**
     * @param class-string $className
     */
    public function extractToMethodParameters(
        mixed $value,
        string $className,
        string $methodName,
        ?Context $context = null
    ): array {
        if (is_object($value)) {
            $value = (array)$value;
        }

        if (!is_array($value)) {
            throw new \RuntimeException();
        }

        $methodDefinition = $this->methodDefinitionParser->getDefinition($className, $methodName);

        $parameters = [];
        $errors = [];

        foreach ($methodDefinition->parameters as $parameter) {
            try {
                /** @psalm-suppress MixedAssignment */
                $parameters[] = $this->extractValue($value, $parameter, $context);
            } catch (BaseTransformingException $e) {
                $errors[] = $e;
            }
        }

        if (!empty($errors)) {
            throw new SameTransformingFieldException($errors, $context ?? new RootContext($className));
        }

        return $parameters;
    }

    /**
     * @template TReturnType
     * @param ValueDefinition<TReturnType> $definition
     * @return TReturnType
     */
    public function extractValue(array $value, ValueDefinition $definition, ?Context $context = null): mixed
    {
        /**
         * @psalm-ignore-var
         * @var Alias|null $alias
         */
        $alias = $definition->attributes->type(Alias::class)->first();
        $name = $alias?->alias ?? $definition->name;

        $parameterContext = new Context($name, previous: $context);

        if (!array_key_exists($name, $value)) {
            if ($definition->required) {
                throw new NotPresentRequiredValueException($parameterContext);
            }

            if (!$definition->hasDefaultValue) {
                throw new NoDefaultValueException($parameterContext);
            }

            return $definition->defaultValue;
        }

        return $this->extractor->extract($value[$name], $definition->type, $definition->attributes, $parameterContext);
    }
}
