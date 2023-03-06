<?php

declare(strict_types=1);

namespace Tochka\Hydrator\ExtendedReflection\Reflectors;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use Tochka\Hydrator\Contracts\AnnotationReaderInterface;
use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\ExtendedReflection\Enums\ClassModifierEnum;
use Tochka\Hydrator\ExtendedReflection\Enums\PropertyModifierEnum;
use Tochka\Hydrator\ExtendedReflection\ExtendedReflectionInterface;
use Tochka\Hydrator\ExtendedReflection\ExtendedTypeFactory;
use Tochka\Hydrator\ExtendedReflection\Traits\DocBlockOperationsTrait;
use Tochka\Hydrator\ExtendedReflection\Traits\ModifiersTrait;

final class ExtendedClassReflection implements ExtendedReflectionInterface
{
    use DocBlockOperationsTrait;
    use ModifiersTrait;

    private \ReflectionClass $reflection;
    private AnnotationReaderInterface $annotationReader;
    private DocBlockFactoryInterface $docBlockFactory;
    private ExtendedTypeFactory $extendedTypeFactory;
    private ?DocBlock $docBlock;

    public function __construct(
        \ReflectionClass $reflectionClass,
        AnnotationReaderInterface $annotationReader,
        DocBlockFactoryInterface $docBlockFactory,
        ExtendedTypeFactory $extendedTypeFactory,
    ) {
        $this->reflection = $reflectionClass;
        $this->annotationReader = $annotationReader;
        $this->docBlockFactory = $docBlockFactory;
        $this->extendedTypeFactory = $extendedTypeFactory;

        $this->docBlock = $this->createDocBlock($reflectionClass, $docBlockFactory);
    }

    /**
     * @return class-string
     */
    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function hasModifiers(ClassModifierEnum $classModifier, ClassModifierEnum ...$classModifiers): bool
    {
        return $this->checkModifiers($this->reflection->getModifiers(), $classModifier, ...$classModifiers);
    }

    /**
     * @param PropertyModifierEnum ...$filter
     * @return Collection<ExtendedPropertyReflection>
     */
    public function getProperties(PropertyModifierEnum ...$filter): Collection
    {
        /** @var int-mask-of<\ReflectionProperty::IS_*> $reflectionFilters */
        $reflectionFilters = array_reduce(
            $filter,
            function (int|null $carry, PropertyModifierEnum $item): int {
                return $carry === null ? $item->getReflectionConst() : $carry | $item->getReflectionConst();
            },
            null
        );

        return new Collection(
            array_map(
                fn (\ReflectionProperty $property) => new ExtendedPropertyReflection(
                    $this,
                    $property,
                    $this->annotationReader,
                    $this->docBlockFactory,
                    $this->extendedTypeFactory,
                ),
                $this->reflection->getProperties($reflectionFilters)
            )
        );
    }

    /**
     * @return Collection<object>
     */
    public function getAttributes(): Collection
    {
        $attributes = $this->annotationReader->getClassMetadata($this->reflection);
        return new Collection($attributes instanceof \Traversable ? iterator_to_array($attributes) : $attributes);
    }

    public function getDescription(): ?string
    {
        return $this->getDescriptionFromDocBlock($this->docBlock);
    }

    public function getReflection(): \ReflectionClass
    {
        return $this->reflection;
    }

    public function getDocBlock(): ?DocBlock
    {
        return $this->docBlock;
    }
}
