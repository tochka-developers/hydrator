<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Hydrators;

use Tochka\Hydrator\Attributes\Alias;
use Tochka\Hydrator\Contracts\ClassDefinitionParserInterface;
use Tochka\Hydrator\Contracts\HydratorInterface;
use Tochka\Hydrator\Contracts\ValueHydratorInterface;
use Tochka\Hydrator\Definitions\DTO\ClassDefinition;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\Exceptions\BaseTransformingException;
use Tochka\Hydrator\Exceptions\SameTransformingFieldException;
use Tochka\TypeParser\Collection;

/**
 * @psalm-api
 */
class NamedObjectHydrator implements ValueHydratorInterface
{
    public function __construct(
        private readonly ClassDefinitionParserInterface $classDefinitionParser,
        private readonly HydratorInterface $hydrator
    ) {
    }

    public function hydrate(mixed $value, Collection $attributes, Context $context, callable $next): mixed
    {
        if (is_object($value) && get_class($value) !== \stdClass::class) {
            $classDefinition = $this->classDefinitionParser->getDefinition(get_class($value));
            $context = new Context($context->name, get_class($value), previous: $context->previous);

            return $this->hydrateObject($value, $classDefinition, $context);
        }

        return $next($value, $attributes, $context);
    }

    private function hydrateObject(object $value, ClassDefinition $classDefinition, Context $context): object
    {
        $result = new \stdClass();
        $errors = [];

        foreach ($classDefinition->properties as $property) {
            try {
                /**
                 * @psalm-ignore-var
                 * @var Alias|null $alias
                 */
                $alias = $property->attributes->type(Alias::class)->first();
                $name = $alias?->alias ?? $property->name;

                $parameterContext = new Context($name, previous: $context);

                $result->{$name} = $this->hydrator->hydrate($value, $property->attributes, $parameterContext);
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
