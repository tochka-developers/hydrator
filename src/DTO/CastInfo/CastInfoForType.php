<?php

namespace Tochka\Hydrator\DTO\CastInfo;

use JetBrains\PhpStorm\Pure;
use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\TypeDefinition;

class CastInfoForType extends AbstractCastInfo
{
    private TypeDefinition $typeDefinition;

    public function __construct(Context $context, TypeDefinition $typeDefinition, ?Collection $attributes = null)
    {
        parent::__construct($context, $attributes);

        $this->typeDefinition = $typeDefinition;
    }

    #[Pure]
    public function getTypeDefinition(): TypeDefinition
    {
        return $this->typeDefinition;
    }

    #[Pure]
    public function getClassName(): ?string
    {
        return $this->typeDefinition->getClassName();
    }
}
