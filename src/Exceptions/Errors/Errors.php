<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions\Errors;

use Tochka\Hydrator\DTO\Context;

class Errors extends Error
{
    public const CODE = 'same_errors';
    public const MESSAGE = 'Same errors while transforming';

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
