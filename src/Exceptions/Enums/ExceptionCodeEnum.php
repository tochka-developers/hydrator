<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions\Enums;

enum ExceptionCodeEnum: int
{
    case EXTENDED_TYPE_FACTORY = 30010;
    case METHOD_NOT_DEFINED = 30020;
    case TRANSFORMING = 30100;
}
