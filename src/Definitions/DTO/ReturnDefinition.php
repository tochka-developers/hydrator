<?php

namespace Tochka\Hydrator\Definitions\DTO;

use Tochka\Hydrator\TypeSystem\TypeInterface;

class ReturnDefinition
{
    private TypeInterface $type;
    private ?string $description = null;

    public function __construct(TypeInterface $type)
    {
        $this->type = $type;
    }

    public function getType(): TypeInterface
    {
        return $this->type;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
