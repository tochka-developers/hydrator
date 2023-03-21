<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Definitions\DTO;

use Tochka\TypeParser\TypeSystem\TypeInterface;

/**
 * @psalm-api
 *
 * @template TReturnType
 */
class ReturnDefinition
{
    /** @var TypeInterface<TReturnType> */
    public readonly TypeInterface $type;
    public ?string $summary = null;
    public ?string $description = null;

    public function __construct(TypeInterface $type)
    {
        $this->type = $type;
    }
}
