<?php

declare(strict_types=1);

namespace Tochka\Hydrator\DTO;

class RootContext extends Context
{
    public function __construct(?string $className = null)
    {
        parent::__construct('', $className);
    }
}
