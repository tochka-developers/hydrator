<?php

namespace Tochka\Hydrator\Support;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\ContextFactory;
use Tochka\Hydrator\Contracts\AnnotationReaderInterface;
use Tochka\Hydrator\Definitions\DTO\Collection;

class ExtendedReflection
{
    private ?DocBlock $docBlock = null;
    private AnnotationReaderInterface $annotationReader;
    private \Reflector $reflector;
    private DocBlockFactory $docBlockFactory;

    public function __construct(
        \Reflector $reflector,
        AnnotationReaderInterface $annotationReader,
        DocBlockFactory $docBlockFactory
    ) {
        $this->reflector = $reflector;
        $this->annotationReader = $annotationReader;
        $this->docBlockFactory = $docBlockFactory;

        if (!method_exists($reflector, 'getDocComment')) {
            return;
        }

        /** @var string|false $docComment */
        $docComment = $reflector->getDocComment();
        if ($docComment === false) {
            return;
        }

        $phpDocContext = (new ContextFactory())->createFromReflector($reflector);
        $this->docBlock = $this->docBlockFactory->create($docComment, $phpDocContext);
    }

    public function getSummary(): ?string
    {
        if (
            !empty($this->docBlock)
            && (
                $this->reflector instanceof \ReflectionMethod
                || $this->reflector instanceof \ReflectionClass
            )
        ) {
            return $this->docBlock->getSummary() ?: null;
        }

        if (
            $this->reflector instanceof \ReflectionProperty
            || $this->reflector instanceof \ReflectionClassConstant
        ) {
            if (!empty($this->docBlock)) {
                $summary = $this->docBlock->getSummary() ?: null;
                if (!empty($summary)) {
                    return $summary;
                }
            }

            /**
             * @psalm-ignore-var
             * @var Var_|null $tag
             */
            $tag = $this->getTags()->type(Var_::class)->first();

            return $tag?->getDescription()?->getBodyTemplate();
        }

        if ($this->reflector instanceof \ReflectionParameter) {
            if (!empty($this->docBlock)) {
                $summary = $this->docBlock->getSummary() ?: null;
                if (!empty($summary)) {
                    return $summary;
                }
            }

            $reflectionMethod = $this->reflector->getDeclaringFunction();
            $docComment = $reflectionMethod->getDocComment();
            $phpDocContext = (new ContextFactory())->createFromReflector($reflectionMethod);
            $docBlock = $this->docBlockFactory->create($docComment, $phpDocContext);
            $reflectionParameterName = $this->reflector->getName();

            /**
             * @psalm-ignore-var
             * @var Param|null $tag
             */
            $tag = $this->getTags($docBlock)
                ->type(Param::class)
                ->filter(fn (Param $tag) => $tag->getVariableName() === $reflectionParameterName)
                ->first();

            return $tag?->getDescription()?->getBodyTemplate();
        }

        return null;
    }

    public function getDescription(): ?string
    {
        if (empty($this->docBlock)) {
            return null;
        }

        $description = $this->docBlock->getDescription()->getBodyTemplate();
        if (!empty($description)) {
            return $description;
        }

        $summary = $this->docBlock->getSummary();

        if (!empty($summary)) {
            return $summary;
        }

        return $this->getSummary();
    }

    /**
     * @param DocBlock|null $docBlock
     * @return Collection<Tag>
     */
    public function getTags(?DocBlock $docBlock = null): Collection
    {
        $docBlock = $docBlock ?? $this->docBlock;
        return new Collection(...($docBlock?->getTags() ?? []));
    }

    /**
     * @return Collection<object>
     */
    public function getAttributes(): Collection
    {
        return new Collection(...$this->getAttributesByReflector());
    }

    /**
     * @return iterable<int, object>
     */
    private function getAttributesByReflector(): iterable
    {
        if ($this->reflector instanceof \ReflectionClass) {
            /** @var iterable<int, object> */
            return $this->annotationReader->getClassMetadata($this->reflector);
        } elseif ($this->reflector instanceof \ReflectionProperty) {
            /** @var iterable<int, object> */
            return $this->annotationReader->getPropertyMetadata($this->reflector);
        } elseif ($this->reflector instanceof \ReflectionFunctionAbstract) {
            /** @var iterable<int, object> */
            return $this->annotationReader->getFunctionMetadata($this->reflector);
        } elseif ($this->reflector instanceof \ReflectionParameter) {
            /** @var iterable<int, object> */
            return $this->annotationReader->getParameterMetadata($this->reflector);
        } elseif ($this->reflector instanceof \ReflectionClassConstant) {
            /** @var iterable<int, object> */
            return $this->annotationReader->getConstantMetadata($this->reflector);
        } else {
            return [];
        }
    }

    public function getReflector(): \Reflector
    {
        return $this->reflector;
    }
}
