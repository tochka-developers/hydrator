<?php

namespace Tochka\Hydrator\Contracts;

use phpDocumentor\Reflection\Type;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\UnionTypeDefinition;

interface TypeDefinitionFactoryInterface
{
    public function getFromReflection(?\ReflectionType $reflectionType): TypeDefinition|UnionTypeDefinition;

    public function getFromDocBlock(Type $typeDefinition): TypeDefinition|UnionTypeDefinition;
}
