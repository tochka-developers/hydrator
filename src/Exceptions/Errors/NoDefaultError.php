<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions\Errors;

use Tochka\Hydrator\DTO\Context;

/**
 * @psalm-api
 */
class NoDefaultError extends Error
{
    public const CODE = 'no_default_value';
    public const MESSAGE = 'Not present value for no default';

    public function __construct(Context $context)
    {
        parent::__construct(self::CODE, self::MESSAGE, $context);
    }
}
