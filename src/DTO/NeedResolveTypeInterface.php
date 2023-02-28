<?php

namespace Tochka\Hydrator\DTO;

interface NeedResolveTypeInterface
{
    public function needResolve(): bool;

    public function setNeedResolve(bool $needResolve): void;
}
