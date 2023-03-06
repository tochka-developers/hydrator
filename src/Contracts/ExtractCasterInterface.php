<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\CastInfo\CastInfoInterface;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\UnionTypeDefinition;

interface ExtractCasterInterface
{
    public function canExtract(CastInfoInterface $castInfo): bool;

    public function extract(CastInfoInterface $castInfo, mixed $value): mixed;

    public function typeBeforeExtract(CastInfoInterface $castInfo): TypeDefinition|UnionTypeDefinition;
}
