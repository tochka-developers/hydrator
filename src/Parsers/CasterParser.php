<?php

namespace Tochka\Hydrator\Parsers;

use Tochka\Hydrator\Annotations\CastBy;
use Tochka\Hydrator\Annotations\ExtractByMethod;
use Tochka\Hydrator\Annotations\HydrateByMethod;
use Tochka\Hydrator\Contracts\CasterRegistryInterface;
use Tochka\Hydrator\Contracts\ExtractCasterInterface;
use Tochka\Hydrator\Contracts\HydrateCasterInterface;
use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\DTO\CallableTypeInterface;
use Tochka\Hydrator\DTO\Caster;
use Tochka\Hydrator\DTO\CastInfo\CastInfoForClass;
use Tochka\Hydrator\DTO\CastInfo\CastInfoForType;
use Tochka\Hydrator\DTO\CastInfo\CastInfoInterface;
use Tochka\Hydrator\DTO\ClassDefinition;
use Tochka\Hydrator\DTO\ParameterDefinition;
use Tochka\Hydrator\DTO\PropertyDefinition;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\ValueDefinition;

class CasterParser
{
    private CasterRegistryInterface $casterRegistry;

    public function __construct(CasterRegistryInterface $casterRegistry)
    {
        $this->casterRegistry = $casterRegistry;
    }

    public function setCasterForParameter(ParameterDefinition $parameterDefinition): void
    {
        /**
         * @psalm-ignore-var
         * @var Collection<CastBy> $attributes
         */
        $attributes = $parameterDefinition->getAttributes()->type(CastBy::class);
        $castInfo = new CastInfoForType($parameterDefinition->getType(), $parameterDefinition->getAttributes());
        foreach ($attributes as $attribute) {
            if (
                $parameterDefinition->getCaster()->getHydrateCaster() === null
                && is_a($attribute->casterClassName, HydrateCasterInterface::class, true)
            ) {
                $castType = $this->casterRegistry->getTypeAfterHydrate($attribute->casterClassName, $castInfo);
                $parameterDefinition->getCaster()->setHydrateCaster($attribute->casterClassName, $castType);
            }

            if (
                $parameterDefinition->getCaster()->getExtractCaster() === null
                && is_a($attribute->casterClassName, ExtractCasterInterface::class, true)
            ) {
                $castType = $this->casterRegistry->getTypeBeforeExtract($attribute->casterClassName, $castInfo);
                $parameterDefinition->getCaster()->setExtractCaster($attribute->casterClassName, $castType);
            }
        }
    }

    public function setCastByMethodForProperty(PropertyDefinition $propertyReference): void
    {
        /**
         * @psalm-ignore-var
         * @var ExtractByMethod $extractAttribute
         */
        $extractAttribute = $propertyReference->getAttributes()->type(ExtractByMethod::class)->first();
        if ($propertyReference->getExtractByMethod() === null && $extractAttribute !== null) {
            $propertyReference->setExtractByMethod($extractAttribute->methodName);
        }

        /**
         * @psalm-ignore-var
         * @var HydrateByMethod $hydrateAttribute
         */
        $hydrateAttribute = $propertyReference->getAttributes()->type(HydrateByMethod::class)->first();
        if ($propertyReference->getHydrateByMethod() === null && $hydrateAttribute !== null) {
            $propertyReference->setHydrateByMethod($hydrateAttribute->methodName);
        }
    }

    public function setGlobalCasterForType(CallableTypeInterface $type, ValueDefinition $valueDefinition): void
    {
        $type->call(function (TypeDefinition $type) use ($valueDefinition) {
            $castInfo = new CastInfoForType($type);

            if ($type->getCaster()->getHydrateCaster() === null) {
                $globalHydrateCaster = $this->casterRegistry->getGlobalHydrateCaster($castInfo);
                if ($globalHydrateCaster !== null) {
                    $castType = $this->casterRegistry->getTypeAfterHydrate($globalHydrateCaster, $castInfo);
                    $type->getCaster()->setHydrateCaster($globalHydrateCaster, $castType);
                }
            }

            if ($type->getCaster()->getExtractCaster() === null) {
                $globalExtractCaster = $this->casterRegistry->getGlobalExtractCaster($castInfo);
                if ($globalExtractCaster !== null) {
                    $castType = $this->casterRegistry->getTypeBeforeExtract($globalExtractCaster, $castInfo);
                    $type->getCaster()->setExtractCaster($globalExtractCaster, $castType);
                }
            }

            if ($type->getValueType() !== null) {
                $this->setGlobalCasterForType($type->getValueType(), $valueDefinition);
            }
        });
    }

    public function setSelfCasterForType(CallableTypeInterface $type): void
    {
        $type->call(function (TypeDefinition $type) {
            if ($type->getClassName() !== null) {
                $this->setSelfCaster(
                    $type->getClassName(),
                    $type->getCaster(),
                    new CastInfoForType($type),
                );
            }

            if ($type->getValueType() !== null) {
                $this->setSelfCasterForType($type->getValueType());
            }
        });
    }

    public function setSelfCasterForClass(ClassDefinition $classDefinition): void
    {
        $this->setSelfCaster(
            $classDefinition->getClassName(),
            $classDefinition->getCaster(),
            new CastInfoForClass($classDefinition, $classDefinition->getAttributes()),
        );
    }

    private function setSelfCaster(string $className, Caster $caster, CastInfoInterface $castInfo): void
    {
        if (
            $caster->getHydrateCaster() === null
            && is_a($className, HydrateCasterInterface::class, true)
        ) {
            $castType = $this->casterRegistry->getTypeAfterHydrate($className, $castInfo);
            $caster->setHydrateCaster($className, $castType);
        }

        if (
            $caster->getExtractCaster() === null
            && is_a($className, ExtractCasterInterface::class, true)
        ) {
            $castType = $this->casterRegistry->getTypeBeforeExtract($className, $castInfo);
            $caster->setExtractCaster($className, $castType);
        }
    }
}
