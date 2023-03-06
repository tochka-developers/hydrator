<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\CastInfo\CastInfoInterface;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\UnionTypeDefinition;

interface HydrateCasterInterface
{
    public function canHydrate(CastInfoInterface $castInfo): bool;

    public function hydrate(CastInfoInterface $castInfo, mixed $value): mixed;

    public function typeAfterHydrate(CastInfoInterface $castInfo): TypeDefinition|UnionTypeDefinition;
}
