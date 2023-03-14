<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Definitions\DTO;

use Tochka\Hydrator\TypeSystem\TypeInterface;

class ReturnDefinition
{
    public readonly TypeInterface $type;
    public ?string $description = null;

    public function __construct(TypeInterface $type)
    {
        $this->type = $type;
    }
}
