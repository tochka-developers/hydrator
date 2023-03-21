<?php

declare(strict_types=1);

namespace Tochka\Hydrator\DTO;

use Tochka\TypeParser\Collection;
use Tochka\TypeParser\TypeSystem\TypeInterface;

/**
 * @template TType
 */
class ToContainer
{
    /**
     * @param TypeInterface<TType> $type
     */
    public function __construct(
        public readonly TypeInterface $type,
        public readonly Collection $attributes
    ) {
    }
}
