<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions\Errors;

use Tochka\Hydrator\DTO\Context;

class UnionTypeError extends Error
{
    public const CODE = 'union_type_resolve';
    public const MESSAGE = 'The actual value does not match any of the union types';

    /** @var array<Error> */
    public readonly array $errors;

    /**
     * @param array<Error> $errors
     */
    public function __construct(array $errors, Context $context)
    {
        parent::__construct(self::CODE, self::MESSAGE, $context);

        $this->errors = $errors;
    }
}
