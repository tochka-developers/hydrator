<?php

namespace Tochka\Hydrator\Exceptions;

class CastException extends BaseHydratorException
{
    public const CODE = 50200;
    public const MESSAGE = 'Error while cast value';

    private array $data;

    public function __construct(?string $message = null, array $data = [])
    {
        parent::__construct($message ?? self::MESSAGE);

        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
