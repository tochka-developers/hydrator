<?php

declare(strict_types=1);

namespace Tochka\Hydrator\ExtendedReflection\Reflectors;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use Tochka\Hydrator\Contracts\AnnotationReaderInterface;
use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\ExtendedReflection\Enums\MethodModifierEnum;
use Tochka\Hydrator\ExtendedReflection\ExtendedReflectionInterface;
use Tochka\Hydrator\ExtendedReflection\ExtendedTypeFactory;
use Tochka\Hydrator\ExtendedReflection\Traits\DocBlockOperationsTrait;
use Tochka\Hydrator\ExtendedReflection\Traits\ModifiersTrait;
use Tochka\Hydrator\TypeSystem\TypeInterface;

class ExtendedMethodReflection implements ExtendedReflectionInterface
{
    use DocBlockOperationsTrait;
    use ModifiersTrait;

    private ExtendedClassReflection $declaringClassReflection;
    private \ReflectionMethod $reflection;
    private AnnotationReaderInterface $annotationReader;
    private DocBlockFactoryInterface $docBlockFactory;
    private ExtendedTypeFactory $extendedTypeFactory;
    private ?DocBlock $docBlock;

    public function __construct(
        ExtendedClassReflection $declaringClassReflection,
        \ReflectionMethod $reflectionMethod,
        AnnotationReaderInterface $annotationReader,
        DocBlockFactoryInterface $docBlockFactory,
        ExtendedTypeFactory $extendedTypeFactory,
    ) {
        $this->declaringClassReflection = $declaringClassReflection;
        $this->reflection = $reflectionMethod;
        $this->annotationReader = $annotationReader;
        $this->docBlockFactory = $docBlockFactory;
        $this->extendedTypeFactory = $extendedTypeFactory;

        $this->docBlock = $this->createDocBlock($reflectionMethod, $docBlockFactory);
    }

    public function getReflection(): \ReflectionMethod
    {
        return $this->reflection;
    }

    public function getDeclaringClass(): ExtendedClassReflection
    {
        return $this->declaringClassReflection;
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    /**
     * @return Collection<ExtendedParameterReflection>
     */
    public function getParameters(): Collection
    {
        return new Collection(
            array_map(
                fn (\ReflectionParameter $parameter) => new ExtendedParameterReflection(
                    $this,
                    $parameter,
                    $this->annotationReader,
                    $this->docBlockFactory,
                    $this->extendedTypeFactory,
                ),
                $this->reflection->getParameters()
            )
        );
    }

    public function getReturnType(): TypeInterface
    {
        return $this->extendedTypeFactory->getType($this);
    }

    public function getReturnDescription(): ?string
    {
        /**
         * @psalm-ignore-var
         * @var Return_|null $returnTag
         */
        $returnTag = $this->getTagsFromDocBlock($this->getDocBlock())
            ->type(Return_::class)
            ->first();

        $description = $returnTag?->getDescription()?->getBodyTemplate();

        return !empty($description) ? $description : null;
    }

    public function hasModifier(MethodModifierEnum $methodModifier, MethodModifierEnum ...$methodModifiers): bool
    {
        return $this->checkModifiers($this->reflection->getModifiers(), $methodModifier, ...$methodModifiers);
    }

    public function getDescription(): ?string
    {
        return $this->getDescriptionFromDocBlock($this->docBlock);
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
        $attributes = $this->annotationReader->getFunctionMetadata($this->reflection);
        return new Collection($attributes instanceof \Traversable ? iterator_to_array($attributes) : $attributes);
    }
}
