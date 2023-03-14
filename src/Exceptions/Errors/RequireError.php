<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions\Errors;

use Tochka\Hydrator\DTO\Context;

class RequireError extends Error
{
    public const CODE = 'not_present_required';
    public const MESSAGE = 'Not present required property';

    public function __construct(Context $context)
    {
        parent::__construct(self::CODE, self::MESSAGE, $context);
    }
}
