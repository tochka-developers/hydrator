<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\UnionTypeDefinition;

/**
 * @psalm-type ClassMatcher = callable(object $objectToResolve): bool
 * @psalm-type InterfaceMatcher = callable(object $objectToResolve): class-string
 */
interface TypeResolverInterface
{
    /**
     * @param class-string $className
     * @param ClassMatcher $match
     * @return void
     */
    public function addClassMatcher(string $className, callable $match): void;

    /**
     * @param class-string $interfaceName
     * @param InterfaceMatcher $match
     * @return void
     */
    public function addInterfaceMatcher(string $interfaceName, callable $match): void;

    public function resolve(
        mixed $valueToResolve,
        TypeDefinition|UnionTypeDefinition $typeDefinition
    ): ?TypeDefinition;
}
