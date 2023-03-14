<?php

namespace Tochka\Hydrator;

class Bar
{
    public function __construct(
        public int $int,
        public string|int $string,
        public array $array = [],
        /** @var array<string> */
        public array $arrayString = [],
    ) {
    }
}
