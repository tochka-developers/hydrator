<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions\Errors;

use Tochka\Hydrator\DTO\Context;

class NoTypeHandlerError extends Error
{
    public const CODE = 'no_type_handler';
    public const MESSAGE = 'No type handler for definition';

    public function __construct(Context $context)
    {
        parent::__construct(self::CODE, self::MESSAGE, $context);
    }
}
