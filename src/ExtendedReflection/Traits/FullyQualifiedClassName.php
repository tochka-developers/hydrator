<?php

namespace Tochka\Hydrator\ExtendedReflection\Traits;

trait FullyQualifiedClassName
{
    /**
     * @param class-string $className
     * @return class-string
     */
    private function fullyQualifiedClassName(string $className): string
    {
        /** @var class-string */
        return trim($className, '\\');
    }
}
