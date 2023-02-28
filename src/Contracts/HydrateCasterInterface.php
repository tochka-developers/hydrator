<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\CastInfo;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\UnionTypeDefinition;

interface HydrateCasterInterface
{
    public function canHydrate(CastInfo $castInfo): bool;

    public function hydrate(CastInfo $castInfo, mixed $value): mixed;

    public function typeAfterHydrate(CastInfo $castInfo): TypeDefinition|UnionTypeDefinition;
}
