<?php

namespace Tochka\Hydrator;

use JetBrains\PhpStorm\Pure;
use Tochka\Hydrator\Definitions\DTO\ClassDefinition;
use Tochka\Hydrator\Definitions\DTO\MethodDefinition;
use Tochka\Hydrator\Definitions\DTO\ReturnDefinition;
use Tochka\Hydrator\Definitions\DTO\ValueDefinition;
use Tochka\Hydrator\ExtendedReflection\ExtendedReflectionInterface;
use Tochka\Hydrator\TypeSystem\TypeInterface;

class NewTest
{
    /**
     * Привет
     * Тут многострочное описание
     *
     * Еще и с переносом
     *
     * И много всего еще
     * @var array
     */
    public array $delays;

    /** @var array<string|MethodDefinition|ValueDefinition> */
    public array $extractors;

    /**
     * А тут просто описание в одну строку
     * Или в две
     */
    public ClassDefinition $hydrator;

    /** @var class-string Вот строка */
    public string $hello = '';

    /**
     * Этот метод занимается всем чем можно
     * @param class-string $hello
     * @param ClassDefinition $class
     * @param positive-int|string $optional
     * @param array<MethodDefinition> $var
     * @return array<ReturnDefinition>
     */
    #[Pure]
    public function test(string $hello, ClassDefinition $class, $optional = false, array $var = []): array
    {

    }
}
