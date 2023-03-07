<?php

declare(strict_types=1);

namespace Tochka\Hydrator\ExtendedReflection\Reflectors;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use Tochka\Hydrator\Contracts\AnnotationReaderInterface;
use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\ExtendedReflection\ExtendedValueReflectionInterface;
use Tochka\Hydrator\ExtendedReflection\ExtendedTypeFactory;
use Tochka\Hydrator\ExtendedReflection\Traits\DocBlockOperationsTrait;
use Tochka\Hydrator\TypeSystem\TypeInterface;

class ExtendedParameterReflection implements ExtendedValueReflectionInterface
{
    use DocBlockOperationsTrait;

    private ExtendedMethodReflection $declaringMethodReflection;
    private \ReflectionParameter $reflection;
    private AnnotationReaderInterface $annotationReader;
    private ExtendedTypeFactory $extendedTypeFactory;
    private ?DocBlock $docBlock;

    public function __construct(
        ExtendedMethodReflection $declaringMethodReflection,
        \ReflectionParameter $reflectionMethod,
        AnnotationReaderInterface $annotationReader,
        DocBlockFactoryInterface $docBlockFactory,
        ExtendedTypeFactory $extendedTypeFactory,
    ) {
        $this->declaringMethodReflection = $declaringMethodReflection;
        $this->reflection = $reflectionMethod;
        $this->annotationReader = $annotationReader;
        $this->extendedTypeFactory = $extendedTypeFactory;

        $this->docBlock = $this->createDocBlock($reflectionMethod, $docBlockFactory);
    }

    public function getReflection(): \ReflectionParameter
    {
        return $this->reflection;
    }

    public function getDeclaringMethod(): ExtendedMethodReflection
    {
        return $this->declaringMethodReflection;
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function getDescription(): ?string
    {
        /**
         * @psalm-ignore-var
         * @var Param|null $paramTag
         */
        $paramTag = $this->getTagsFromDocBlock($this->getDeclaringMethod()->getDocBlock())
            ->type(Param::class)
            ->filter(fn (Param $param) => $param->getName() === $this->getName())
            ->first();

        $description = $paramTag?->getDescription()?->getBodyTemplate();

        return !empty($description) ? $description : null;
    }

    public function getType(): TypeInterface
    {
        return $this->extendedTypeFactory->getType($this);
    }

    public function hasDefaultValue(): bool
    {
        return $this->reflection->isOptional();
    }

    public function isRequired(): bool
    {
        return !$this->reflection->isOptional();
    }

    public function getDefaultValue(): mixed
    {
        try {
            return $this->reflection->getDefaultValue();
        } catch (\ReflectionException) {
            return null;
        }
    }

    public function getDocBlock(): ?DocBlock
    {
        return $this->docBlock;
    }

    /**
     * @return Collection<object>
     */
    public function getAttributes(): Collection
    {
        return new Collection([]);
    }
}
