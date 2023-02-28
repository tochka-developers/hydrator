<?php

namespace Tochka\Hydrator;

use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Tochka\Hydrator\Contracts\ClassDefinitionsRegistryInterface;
use Tochka\Hydrator\Contracts\DefinitionParserInterface;
use Tochka\Hydrator\Contracts\ExtendedReflectionFactoryInterface;
use Tochka\Hydrator\Contracts\TypeDefinitionFactoryInterface;
use Tochka\Hydrator\DTO\CallableTypeInterface;
use Tochka\Hydrator\DTO\ClassDefinition;
use Tochka\Hydrator\DTO\ParameterDefinition;
use Tochka\Hydrator\DTO\PropertyDefinition;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\ValueDefinition;
use Tochka\Hydrator\Parsers\CasterParser;
use Tochka\Hydrator\Parsers\IterableTypesParser;
use Tochka\Hydrator\Support\ExtendedReflection;

class DefinitionParser implements DefinitionParserInterface
{
    private ClassDefinitionsRegistryInterface $classDefinitionsRegistry;
    private ExtendedReflectionFactoryInterface $extendedReflectionFactory;
    private TypeDefinitionFactoryInterface $typeDefinitionFactory;
    private IterableTypesParser $iterableTypesParser;
    private CasterParser $casterParser;

    public function __construct(
        ClassDefinitionsRegistryInterface $classDefinitionsRegistry,
        ExtendedReflectionFactoryInterface $extendedReflectionFactory,
        TypeDefinitionFactoryInterface $typeDefinitionFactory,
        IterableTypesParser $iterableTypesParser,
        CasterParser $casterParser,
    ) {
        $this->classDefinitionsRegistry = $classDefinitionsRegistry;
        $this->extendedReflectionFactory = $extendedReflectionFactory;
        $this->typeDefinitionFactory = $typeDefinitionFactory;
        $this->iterableTypesParser = $iterableTypesParser;
        $this->casterParser = $casterParser;
    }

    /**
     * @throws \ReflectionException
     */
    public function getMethodParameters(string $className, string $methodName): array
    {
        $reflectionMethod = new \ReflectionMethod($className, $methodName);
        $extendedReflectionMethod = $this->extendedReflectionFactory->make($reflectionMethod);

        $parameters = [];
        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $parameters[] = $this->getMethodParameter($reflectionParameter, $extendedReflectionMethod);
        }

        return $parameters;
    }

    private function getMethodParameter(
        \ReflectionParameter $reflectionParameter,
        ExtendedReflection $extendedReflectionMethod
    ): ParameterDefinition {
        $extendedReflectionParameter = $this->extendedReflectionFactory->make($reflectionParameter);
        /**
         * @psalm-ignore-var
         * @var Param|null $paramTag
         */
        $paramTag = $extendedReflectionMethod->getTags()
            ->type(Param::class)
            ->filter(fn (Param $tag) => $tag->getVariableName() === $reflectionParameter->getName())
            ->first();

        $reflectionType = $reflectionParameter->getType();
        $type = $this->typeDefinitionFactory->getFromReflection($reflectionType);

        $parameterDefinition = new ParameterDefinition($reflectionParameter->getName(), $type);
        $parameterDefinition->setRequired(!$reflectionParameter->isOptional());
        if ($reflectionParameter->isDefaultValueAvailable()) {
            $parameterDefinition->setDefaultValue($reflectionParameter->getDefaultValue());
        }
        $parameterDefinition->setAttributes($extendedReflectionParameter->getAttributes());
        $this->setDescription($parameterDefinition, $paramTag);

        $this->iterableTypesParser->parse($parameterDefinition->getType(), $paramTag?->getType());

        $this->casterParser->setCasterForParameter($parameterDefinition);
        $this->casterParser->setSelfCasterForType($parameterDefinition->getType());
        $this->casterParser->setGlobalCasterForType($parameterDefinition->getType(), $parameterDefinition);

        $this->addTypeToClassDefinitionRegistry($parameterDefinition->getType());

        return $parameterDefinition;
    }

    /**
     * @throws \ReflectionException
     */
    public function getMethodReturn(string $className, string $methodName): ValueDefinition
    {
        $reflectionMethod = new \ReflectionMethod($className, $methodName);
        $extendedReflectionMethod = $this->extendedReflectionFactory->make($reflectionMethod);
        /**
         * @psalm-ignore-var
         * @var Return_|null $returnTag
         */
        $returnTag = $extendedReflectionMethod->getTags()->type(Return_::class)->first();

        $reflectionType = $reflectionMethod->getReturnType();

        $type = $this->typeDefinitionFactory->getFromReflection($reflectionType);
        $value = new ValueDefinition($type);
        $this->setDescription($value, $returnTag);

        $this->iterableTypesParser->parse($value->getType(), $returnTag?->getType());

        $this->casterParser->setSelfCasterForType($value->getType());
        $this->casterParser->setGlobalCasterForType($value->getType(), $value);

        $this->addTypeToClassDefinitionRegistry($value->getType());

        return $value;
    }

    /**
     * @throws \ReflectionException
     */
    public function getClassProperties(string $className): array
    {
        $reflectionClass = new \ReflectionClass($className);

        $parameters = [];
        foreach ($reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $parameters[] = $this->getClassPropertyDefinition($reflectionProperty);
        }

        return $parameters;
    }

    private function getClassPropertyDefinition(\ReflectionProperty $reflectionProperty): PropertyDefinition
    {
        $extendedReflectionProperty = $this->extendedReflectionFactory->make($reflectionProperty);
        /**
         * @psalm-ignore-var
         * @var Var_|null $varTag
         */
        $varTag = $extendedReflectionProperty->getTags()->type(Var_::class)->first();

        $reflectionType = $reflectionProperty->getType();
        $type = $this->typeDefinitionFactory->getFromReflection($reflectionType);

        $propertyDefinition = new PropertyDefinition(
            $reflectionProperty->getDeclaringClass()->getName(),
            $reflectionProperty->getName(),
            $type
        );
        $propertyDefinition->setRequired(!$reflectionProperty->hasDefaultValue());
        if ($reflectionProperty->hasDefaultValue()) {
            $propertyDefinition->setDefaultValue($reflectionProperty->getDefaultValue());
        }
        $propertyDefinition->setAttributes($extendedReflectionProperty->getAttributes());
        $this->setDescription($propertyDefinition, $varTag);

        $this->iterableTypesParser->parse($propertyDefinition->getType(), $varTag?->getType());

        $this->casterParser->setCastByMethodForProperty($propertyDefinition);
        $this->casterParser->setCasterForParameter($propertyDefinition);
        $this->casterParser->setSelfCasterForType($propertyDefinition->getType());
        $this->casterParser->setGlobalCasterForType($propertyDefinition->getType(), $propertyDefinition);

        $this->addTypeToClassDefinitionRegistry($propertyDefinition->getType());

        return $propertyDefinition;
    }

    /**
     * @throws \ReflectionException
     */
    public function getClassDefinition(string $className): ClassDefinition
    {
        return $this->extendClassDefinition(new ClassDefinition($className));
    }

    /**
     * @throws \ReflectionException
     */
    private function extendClassDefinition(ClassDefinition $classDefinition): ClassDefinition
    {
        $extendedReflectionClass = $this->extendedReflectionFactory->makeForClass($classDefinition->getClassName());

        $this->casterParser->setSelfCasterForClass($classDefinition);

        $classDefinition->setProperties($this->getClassProperties($classDefinition->getClassName()));
        $description = $extendedReflectionClass->getSummary();
        if ($description !== null) {
            $classDefinition->setDescription($description);
        }

        $classDefinition->setAttributes($extendedReflectionClass->getAttributes());

        return $classDefinition;
    }

    private function setDescription(ValueDefinition $valueDefinition, ?BaseTag $tag): void
    {
        $description = $tag?->getDescription()?->getBodyTemplate();
        if (!empty($description)) {
            $valueDefinition->setDescription($description);
        }
    }

    private function addTypeToClassDefinitionRegistry(CallableTypeInterface $type): void
    {
        $type->call(function (TypeDefinition $type) {
            if ($type->getCaster()->getHydrateCaster() !== null && $type->getCaster()->getExtractCaster() !== null) {
                return;
            }

            if ($type->getClassName() !== null && interface_exists($type->getClassName())) {
                // parse all interface implements
                foreach (get_declared_classes() as $className) {
                    if (in_array($type->getClassName(), class_implements($className))) {
                        $this->addClassToRegistry($className);
                    }
                }
            }

            if ($type->getClassName() !== null && !$type->needResolve()) {
                $this->addClassToRegistry($type->getClassName());
            }

            if ($type->getValueType() !== null) {
                $this->addTypeToClassDefinitionRegistry($type->getValueType());
            }
        });
    }

    /**
     * @param class-string $className
     * @return void
     * @throws \ReflectionException
     */
    private function addClassToRegistry(string $className): void
    {
        if ($this->classDefinitionsRegistry->has($className)) {
            return;
        }

        $classDefinition = new ClassDefinition($className);
        $this->classDefinitionsRegistry->add($classDefinition);
        $this->extendClassDefinition($classDefinition);
    }
}
