<?php

declare(strict_types=1);

namespace Tochka\Hydrator\ExtendedReflection\Traits;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Types\ContextFactory;
use Tochka\Hydrator\Definitions\DTO\Collection;

trait DocBlockOperationsTrait
{
    private function createDocBlock(\Reflector $reflector, DocBlockFactoryInterface $docBlockFactory): ?DocBlock
    {
        if (!method_exists($reflector, 'getDocComment')) {
            return null;
        }

        $docComment = $reflector->getDocComment();

        if ($docComment === false) {
            return null;
        }

        $context = (new ContextFactory())->createFromReflector($reflector);
        try {
            return $docBlockFactory->create($docComment, $context);
        } catch (\Throwable) {
            return null;
        }
    }

    private function getDescriptionFromDocBlock(?DocBlock $docBlock): ?string
    {
        if ($docBlock === null) {
            return null;
        }

        $description = $docBlock->getDescription()->getBodyTemplate();

        if (!empty($description)) {
            return $description;
        }

        $summary = $docBlock->getSummary();

        if (!empty($summary)) {
            return $summary;
        }

        return null;
    }

    private function getTagsFromDocBlock(?DocBlock $docBlock): Collection
    {
        if ($docBlock === null) {
            return new Collection([]);
        }

        return new Collection($docBlock->getTags());
    }
}
