<?php

namespace Tochka\Hydrator\DTO\CastInfo;

use JetBrains\PhpStorm\Pure;
use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\DTO\ClassDefinition;
use Tochka\Hydrator\DTO\Context;

class CastInfoForClass extends AbstractCastInfo
{
    private ClassDefinition $classDefinition;

    public function __construct(Context $context, ClassDefinition $classDefinition, ?Collection $attributes = null)
    {
        parent::__construct($context, $attributes);

        $this->classDefinition = $classDefinition;
    }

    #[Pure]
    public function getClassDefinition(): ClassDefinition
    {
        return $this->classDefinition;
    }
    
    #[Pure]
    public function getClassName(): ?string
    {
        return $this->classDefinition->getClassName();
    }
}
