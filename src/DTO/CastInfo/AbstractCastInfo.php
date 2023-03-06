<?php

namespace Tochka\Hydrator\DTO\CastInfo;

use JetBrains\PhpStorm\Pure;
use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\DTO\Context;

abstract class AbstractCastInfo implements CastInfoInterface
{
    /** @var Collection<object> */
    private Collection $attributes;
    private Context $context;

    public function __construct(Context $context, ?Collection $attributes = null)
    {
        $this->context = $context;
        $this->attributes = $attributes ?? new Collection();
    }

    #[Pure]
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    #[Pure]
    public function getContext(): Context
    {
        return $this->context;
    }
}
