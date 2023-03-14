<?php

declare(strict_types=1);

namespace Tochka\Hydrator\ExtendedReflection;

use phpDocumentor\Reflection\DocBlock;
use Tochka\Hydrator\Definitions\DTO\Collection;

interface ExtendedReflectionInterface
{
    public function getName(): string;

    public function getDescription(): ?string;

    public function getReflection(): \Reflector;

    public function getDocBlock(): ?DocBlock;

    /**
     * @return Collection<object>
     */
    public function getAttributes(): Collection;
}
