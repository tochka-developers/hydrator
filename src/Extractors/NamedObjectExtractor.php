<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ClassDefinitionParserInterface;
use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\Definitions\DTO\ClassDefinition;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\BaseTransformingException;
use Tochka\Hydrator\Exceptions\MakeTargetException;
use Tochka\Hydrator\Exceptions\SameTransformingFieldException;
use Tochka\Hydrator\Exceptions\UnexpectedTypeException;
use Tochka\Hydrator\ExtractFactory;
use Tochka\TypeParser\TypeSystem\Types\NamedObjectType;

/**
 * @psalm-api
 */
final class NamedObjectExtractor implements ValueExtractorInterface
{
    public function __construct(
        private readonly ClassDefinitionParserInterface $classDefinitionParser,
        private readonly ExtractFactory $extractor
    ) {
    }

    public function extract(mixed $value, ToContainer $to, Context $context, callable $next): mixed
    {
        /** @psalm-suppress RedundantCondition */
        if (!$to->type instanceof NamedObjectType) {
            return $next($value, $to, $context);
        }

        if (!is_object($value) && !is_array($value)) {
            throw new UnexpectedTypeException(gettype($value), 'array|object', $context);
        }

        $value = (array)$value;

        $classDefinition = $this->classDefinitionParser->getDefinition($to->type->className);
        $context = new Context($context->name, $to->type->className, previous: $context->previous);

        return $this->extractObject($value, $classDefinition, $context);
    }

    private function extractObject(array $value, ClassDefinition $classDefinition, Context $context): object
    {
        try {
            $reflection = new \ReflectionClass($classDefinition->className);
            $result = $reflection->newInstanceWithoutConstructor();
        } catch (\ReflectionException $e) {
            throw new MakeTargetException($classDefinition->className, $context, $e);
        }

        $errors = [];

        foreach ($classDefinition->properties as $property) {
            try {
                $result->{$property->name} = $this->extractor->extractValue($value, $property, $context);
            } catch (BaseTransformingException $e) {
                $errors[] = $e;
            }
        }

        if (!empty($errors)) {
            throw new SameTransformingFieldException($errors, $context);
        }

        return $result;
    }
}
