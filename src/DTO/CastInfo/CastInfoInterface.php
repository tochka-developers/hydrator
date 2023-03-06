<?php

namespace Tochka\Hydrator\DTO\CastInfo;

use JetBrains\PhpStorm\Pure;
use Tochka\Hydrator\Definitions\DTO\Collection;

interface CastInfoInterface
{
    /**
     * @return Collection<object>
     */
    #[Pure]
    public function getAttributes(): Collection;

    /**
     * @return class-string|null
     */
    #[Pure]
    public function getClassName(): ?string;
}
