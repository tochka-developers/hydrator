<?php

declare(strict_types=1);

namespace Tochka\Hydrator\ExtendedReflection\Reflectors;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use Tochka\Hydrator\Contracts\AnnotationReaderInterface;
use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\ExtendedReflection\Enums\PropertyModifierEnum;
use Tochka\Hydrator\ExtendedReflection\ExtendedReflectionWithTypeInterface;
use Tochka\Hydrator\ExtendedReflection\ExtendedTypeFactory;
use Tochka\Hydrator\ExtendedReflection\Traits\DocBlockOperationsTrait;
use Tochka\Hydrator\ExtendedReflection\Traits\ModifiersTrait;
use Tochka\Hydrator\TypeSystem\TypeInterface;

class ExtendedPropertyReflection implements ExtendedReflectionWithTypeInterface
{
    use DocBlockOperationsTrait;
    use ModifiersTrait;

    private ExtendedClassReflection $declaringClassReflection;
    private \ReflectionProperty $reflection;
    private AnnotationReaderInterface $annotationReader;
    private ?DocBlock $docBlock;
    private DocBlockFactoryInterface $docBlockFactory;
    private ExtendedTypeFactory $extendedTypeFactory;

    public function __construct(
        ExtendedClassReflection $declaringClassReflection,
        \ReflectionProperty $reflectionProperty,
        AnnotationReaderInterface $annotationReader,
        DocBlockFactoryInterface $docBlockFactory,
        ExtendedTypeFactory $extendedTypeFactory,
    ) {
        $this->declaringClassReflection = $declaringClassReflection;
        $this->reflection = $reflectionProperty;
        $this->annotationReader = $annotationReader;
        $this->docBlockFactory = $docBlockFactory;
        $this->extendedTypeFactory = $extendedTypeFactory;

        $this->docBlock = $this->createDocBlock($this->reflection, $docBlockFactory);
    }

    public function getReflection(): \ReflectionProperty
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

    public function getType(): TypeInterface
    {
        return $this->extendedTypeFactory->getType($this);
    }

    public function hasDefaultValue(): bool
    {
        if ($this->reflection->isPromoted()) {
            $promotedParameter = $this->getPromotedParameter();
            if ($promotedParameter === null) {
                return false;
            }

            return $promotedParameter->hasDefaultValue();
        }

        return $this->reflection->hasDefaultValue();
    }

    public function isRequired(): bool
    {
        return !$this->hasDefaultValue();
    }

    public function getDefaultValue(): mixed
    {
        if ($this->reflection->isPromoted()) {
            $promotedParameter = $this->getPromotedParameter();
            if ($promotedParameter === null) {
                return false;
            }

            return $promotedParameter->getDefaultValue();
        }

        return $this->reflection->getDefaultValue();
    }

    public function getDescription(): ?string
    {
        return $this->getDescriptionFromDocBlock($this->docBlock);
    }

    public function hasModifier(
        PropertyModifierEnum $propertyModifier,
        PropertyModifierEnum ...$propertyModifiers
    ): bool {
        return $this->checkModifiers($this->reflection->getModifiers(), $propertyModifier, ...$propertyModifiers);
    }

    /**
     * @return Collection<object>
     */
    public function getAttributes(): Collection
    {
        $attributes = $this->annotationReader->getPropertyMetadata($this->reflection);
        return new Collection($attributes instanceof \Traversable ? iterator_to_array($attributes) : $attributes);
    }

    private function getPromotedParameter(): ?ExtendedParameterReflection
    {
        $constructor = $this->reflection->getDeclaringClass()->getConstructor();
        if ($constructor === null) {
            return null;
        }

        $constructorReflection = new ExtendedMethodReflection(
            $this->declaringClassReflection,
            $constructor,
            $this->annotationReader,
            $this->docBlockFactory,
            $this->extendedTypeFactory,
        );
        /**
         * @var ExtendedParameterReflection
         */
        return $constructorReflection->getParameters()
            ->filter(fn (ExtendedParameterReflection $parameter) => $parameter->getName() === $this->getName())
            ->first();
    }

    public function getDocBlock(): ?DocBlock
    {
        return $this->docBlock;
    }
}
