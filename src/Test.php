<?php

namespace Tochka\Hydrator;

class Test
{
    public function __construct(
        public int $testPromoted,
        public string $testPromotedWithDefault = 'hello',
        public NewTest $test,
        /** @var array */
        public array $delays,

        /** @var array<Extractor> */
        public array $extractors,
    ) {}
}
