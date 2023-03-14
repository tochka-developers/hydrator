<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\UnexpectedTypeException;
use Tochka\Hydrator\TypeSystem\Types\NullType;

final class NullExtractor implements ValueExtractorInterface
{
    public function extract(FromContainer $from, ToContainer $to, Context $context, callable $next): mixed
    {
        if (!$to->type instanceof NullType) {
            return $next($from, $to, $context);
        }

        if ($from->value !== null) {
            throw new UnexpectedTypeException($to->type, $from->type, $context);
        }

        return null;
    }
}
