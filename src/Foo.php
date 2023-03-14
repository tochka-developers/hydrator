<?php

namespace Tochka\Hydrator;

use Tochka\Hydrator\Attributes\ResolveBy;
use Tochka\Hydrator\TypeSystem\TypeInterface;
use Tochka\Hydrator\TypeSystem\Types\IntType;
use Tochka\Hydrator\TypeSystem\Types\NamedObjectType;
use Tochka\Hydrator\TypeSystem\Types\StringType;

class Foo
{
    public array $array;

    /** @var array<string> */
    public array $arrayOfString;

    public string $string;

    public int $int;

    public float $float;

    public bool $bool;

    public Bar $bar;

    /** @var array<Bar>|array<string> */
    public array $arrayOfBar;

    public object $nullableOne;
    /**
     * @var string|int|Bar|Test
     */
    #[ResolveBy]
    public string|int|Bar|Test $test;
    public ?string $nullableTwo;

    public function resolve(mixed $value, TypeInterface $type): bool
    {
        if (is_string($value) && $type instanceof StringType) {
            return true;
        }

        if (is_int($value) && $type instanceof IntType) {
            return true;
        }

        if (!$type instanceof NamedObjectType) {
            return false;
        }

        if (is_object($value)) {
            $value = (array)$value;
        }

        if (is_array($value)) {
            if (($value['type'] ?? '') === 'bar' && $type->className === Bar::class) {
                return true;
            }

            if (($value['type'] ?? '') === 'test' && $type->className === Test::class) {
                return true;
            }
        }

        return false;
    }
}
