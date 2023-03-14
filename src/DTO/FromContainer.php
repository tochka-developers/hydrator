<?php

declare(strict_types=1);

namespace Tochka\Hydrator\DTO;

use Tochka\Hydrator\TypeSystem\TypeInterface;

/**
 * @template TValueType
 */
class FromContainer
{
    /**
     * @param TValueType $value
     * @param TypeInterface<TValueType> $type
     */
    public function __construct(
        public readonly mixed $value,
        public readonly TypeInterface $type
    ) {
    }
}
