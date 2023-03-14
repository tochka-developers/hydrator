<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\ToContainer;

final class DateTimeExtractor implements ValueExtractorInterface
{
    public function extract(FromContainer $from, ToContainer $to, Context $context, callable $next): mixed
    {
        return $next($from, $to, $context);
    }
}
