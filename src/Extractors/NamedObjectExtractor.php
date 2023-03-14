<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ClassDefinitionParserInterface;
use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\Definitions\DTO\ClassDefinition;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\BaseTransformingException;
use Tochka\Hydrator\Exceptions\MakeTargetException;
use Tochka\Hydrator\Exceptions\SameTransformingFieldException;
use Tochka\Hydrator\Exceptions\UnexpectedTypeException;
use Tochka\Hydrator\ExtractFactory;
use Tochka\Hydrator\TypeSystem\Types\ArrayType;
use Tochka\Hydrator\TypeSystem\Types\NamedObjectType;
use Tochka\Hydrator\TypeSystem\Types\ObjectType;

final class NamedObjectExtractor implements ValueExtractorInterface
{

    public function __construct(
        private readonly ClassDefinitionParserInterface $classDefinitionParser,
        private readonly ExtractFactory $extractor
    ) {
    }

    public function extract(FromContainer $from, ToContainer $to, Context $context, callable $next): mixed
    {
        if (!$to->type instanceof NamedObjectType) {
            return $next($from, $to, $context);
        }

        if (!$from->type instanceof ObjectType && !$from->type instanceof ArrayType) {
            throw new UnexpectedTypeException($to->type, $from->type, $context);
        }

        $value = (array)$from->value;

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
