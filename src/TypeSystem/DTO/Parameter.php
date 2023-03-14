<?php

declare(strict_types=1);

namespace Tochka\Hydrator\TypeSystem\DTO;

use Tochka\Hydrator\TypeSystem\TypeInterface;
use Tochka\Hydrator\TypeSystem\Types\MixedType;

/**
 * @psalm-api
 * @psalm-immutable
 * @template-covariant TType
 */
final class Parameter
{
    public readonly TypeInterface $type;
    public readonly bool $default;
    public readonly bool $variadic;

    /**
     * @param TypeInterface<TType> $type
     */
    public function __construct(TypeInterface $type = new MixedType(), bool $default = false, bool $variadic = false)
    {
        if (!($default && $variadic)) {
            // TODO: exception
            throw new \LogicException('Parameter can be either default or variadic.');
        }

        $this->type = $type;
        $this->default = $default;
        $this->variadic = $variadic;
    }
}
