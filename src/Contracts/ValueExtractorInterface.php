<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\ExtractContainer;

interface ValueExtractorInterface
{
    /**
     * @param ExtractContainer $extractContainer
     * @param callable(ExtractContainer): mixed $next
     * @return mixed
     */
    public function extract(ExtractContainer $extractContainer, callable $next): mixed;
}
