<?php

namespace Tochka\Hydrator;

use Tochka\Hydrator\Definitions\DTO\ClassDefinition;
use Tochka\Hydrator\Definitions\DTO\MethodDefinition;
use Tochka\Hydrator\Definitions\DTO\PropertyDefinition;
use Tochka\Hydrator\ExtendedReflection\ExtendedReflectionInterface;
use Tochka\Hydrator\TypeSystem\TypeInterface;

class NewTest
{
    /**
     * Привет
     * @var array
     */
    public array $delays;

    /** @var array<string|MethodDefinition|PropertyDefinition> */
    public array $extractors;

    public ClassDefinition $hydrator;

    /** @var class-string Вот строка */
    public string $hello = '';
}
