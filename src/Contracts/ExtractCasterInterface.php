<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\CastInfo;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\UnionTypeDefinition;

interface ExtractCasterInterface
{
    public function canExtract(CastInfo $castInfo): bool;

    public function extract(CastInfo $castInfo, mixed $value): mixed;

    public function typeBeforeExtract(CastInfo $castInfo): TypeDefinition|UnionTypeDefinition;
}
