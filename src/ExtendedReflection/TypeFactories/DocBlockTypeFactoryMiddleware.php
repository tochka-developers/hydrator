<?php

namespace Tochka\Hydrator\ExtendedReflection\TypeFactories;

use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\PseudoTypes\CallableString;
use phpDocumentor\Reflection\PseudoTypes\False_;
use phpDocumentor\Reflection\PseudoTypes\HtmlEscapedString;
use phpDocumentor\Reflection\PseudoTypes\IntegerRange;
use phpDocumentor\Reflection\PseudoTypes\List_;
use phpDocumentor\Reflection\PseudoTypes\LiteralString;
use phpDocumentor\Reflection\PseudoTypes\LowercaseString;
use phpDocumentor\Reflection\PseudoTypes\NegativeInteger;
use phpDocumentor\Reflection\PseudoTypes\NonEmptyLowercaseString;
use phpDocumentor\Reflection\PseudoTypes\NonEmptyString;
use phpDocumentor\Reflection\PseudoTypes\NumericString;
use phpDocumentor\Reflection\PseudoTypes\PositiveInteger;
use phpDocumentor\Reflection\PseudoTypes\TraitString;
use phpDocumentor\Reflection\PseudoTypes\True_;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\AbstractList;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\ClassString;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\InterfaceString;
use phpDocumentor\Reflection\Types\Intersection;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Never_;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\Resource_;
use phpDocumentor\Reflection\Types\Scalar;
use phpDocumentor\Reflection\Types\Self_;
use phpDocumentor\Reflection\Types\Static_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\Void_;
use Tochka\Hydrator\ExtendedReflection\ExtendedReflectionInterface;
use Tochka\Hydrator\ExtendedReflection\Reflectors\ExtendedMethodReflection;
use Tochka\Hydrator\ExtendedReflection\Reflectors\ExtendedParameterReflection;
use Tochka\Hydrator\ExtendedReflection\Reflectors\ExtendedPropertyReflection;
use Tochka\Hydrator\ExtendedReflection\Traits\DocBlockOperationsTrait;
use Tochka\Hydrator\ExtendedReflection\Traits\FullyQualifiedClassName;
use Tochka\Hydrator\TypeSystem\DTO\StringRestrictionEnum;
use Tochka\Hydrator\TypeSystem\TypeComparator;
use Tochka\Hydrator\TypeSystem\TypeInterface;
use Tochka\Hydrator\TypeSystem\Types\ArrayKeyType;
use Tochka\Hydrator\TypeSystem\Types\ArrayType;
use Tochka\Hydrator\TypeSystem\Types\BoolType;
use Tochka\Hydrator\TypeSystem\Types\CallableType;
use Tochka\Hydrator\TypeSystem\Types\FalseType;
use Tochka\Hydrator\TypeSystem\Types\FloatType;
use Tochka\Hydrator\TypeSystem\Types\IntersectionType;
use Tochka\Hydrator\TypeSystem\Types\IntType;
use Tochka\Hydrator\TypeSystem\Types\MixedType;
use Tochka\Hydrator\TypeSystem\Types\NamedObjectType;
use Tochka\Hydrator\TypeSystem\Types\NeverType;
use Tochka\Hydrator\TypeSystem\Types\NullType;
use Tochka\Hydrator\TypeSystem\Types\ObjectType;
use Tochka\Hydrator\TypeSystem\Types\ResourceType;
use Tochka\Hydrator\TypeSystem\Types\ScalarType;
use Tochka\Hydrator\TypeSystem\Types\StringType;
use Tochka\Hydrator\TypeSystem\Types\TrueType;
use Tochka\Hydrator\TypeSystem\Types\UnionType;
use Tochka\Hydrator\TypeSystem\Types\VoidType;

class DocBlockTypeFactoryMiddleware implements TypeFactoryMiddlewareInterface
{
    use DocBlockOperationsTrait;
    use FullyQualifiedClassName;

    private TypeComparator $typeComparator;

    public function __construct(TypeComparator $typeComparator)
    {
        $this->typeComparator = $typeComparator;
    }

    public function handle(
        TypeInterface $defaultType,
        ExtendedReflectionInterface $reflector,
        callable $next
    ): TypeInterface {
        $type = $this->getDocBlockType($reflector);

        if ($type === null || $defaultType instanceof IntersectionType) {
            return $defaultType;
        }

        if ($defaultType instanceof UnionType) {
            // ?????? ?????????????? ???????? ???? ?????????????????????? ???????? ?????????? ?? ???????????? ?????????? phpdoc ?????????????????????????????? ?? ?????????????????????? ?? ?????????????? ???????? ???????????????? ??????
            $typeFromDocBlock = $this->clarifyType(new MixedType(), $type, $reflector);
            $resultTypes = [];

            foreach ($defaultType->types as $subType) {
                $clarifiedType = $subType;
                if ($typeFromDocBlock instanceof UnionType) {
                    foreach ($typeFromDocBlock->types as $subDocType) {
                        if ($this->typeComparator->compare($subType, $subDocType)) {
                            $clarifiedType = $subDocType;
                            break;
                        }
                    }
                } elseif ($this->typeComparator->compare($subType, $typeFromDocBlock)) {
                    $clarifiedType = $typeFromDocBlock;
                }
                $resultTypes[] = $clarifiedType;
            }

            return new UnionType(...$resultTypes);
        }

        return $next($this->clarifyType($defaultType, $type, $reflector), $reflector);
    }

    private function getDocBlockType(ExtendedReflectionInterface $reflector): ?Type
    {
        if ($reflector instanceof ExtendedParameterReflection) {
            /**
             * @psalm-ignore-var
             * @var Param|null $paramTag
             */
            $paramTag = $this->getTagsFromDocBlock($reflector->getDeclaringMethod()->getDocBlock())
                ->type(Param::class)
                ->filter(fn (Param $param) => $param->getVariableName() === $reflector->getName())
                ->first();

            return $paramTag?->getType();
        }

        if ($reflector instanceof ExtendedPropertyReflection) {
            $propertyTag = $this->getTagsFromDocBlock($reflector->getDocBlock())
                ->type(Var_::class)
                ->first();

            return $propertyTag?->getType();
        }

        if ($reflector instanceof ExtendedMethodReflection) {
            $returnTag = $this->getTagsFromDocBlock($reflector->getDocBlock())
                ->type(Return_::class)
                ->first();

            return $returnTag?->getType();
        }

        return null;
    }

    private function clarifyType(
        TypeInterface $defaultType,
        Type $type,
        ExtendedReflectionInterface $reflector
    ): TypeInterface {
        if ($type instanceof Compound) {
            // if default type is not mixed, and doc block contains aggregate type - this is conflict, return default type
            if (!$defaultType instanceof MixedType) {
                return $defaultType;
            }

            return new UnionType(...$this->getMultipleTypes($defaultType, $type, $reflector));
        }

        if ($type instanceof Intersection) {
            // if default type is not mixed, and doc block contains aggregate type - this is conflict, return default type
            if (!$defaultType instanceof MixedType) {
                return $defaultType;
            }

            return new IntersectionType(...$this->getMultipleTypes($defaultType, $type, $reflector));
        }

        $clarifyType = match ($type::class) {
            Array_::class,
            Iterable_::class,
            List_::class => $this->clarifyArrayType($type, $reflector),
            Boolean::class => new BoolType(),
            Callable_::class => new CallableType(),
            False_::class => new FalseType(),
            Float_::class => new FloatType(),
            Integer::class,
            IntegerRange::class,
            NegativeInteger::class,
            PositiveInteger::class => $this->clarifyIntegerType($type),
            Never_::class => new NeverType(),
            Null_::class => new NullType(),
            Object_::class,
            Self_::class,
            Static_::class => $this->clarifyObjectType($type, $reflector),
            Resource_::class => new ResourceType(),
            String_::class,
            ClassString::class,
            LiteralString::class,
            NonEmptyLowercaseString::class,
            TraitString::class,
            LowercaseString::class,
            CallableString::class,
            HtmlEscapedString::class,
            NumericString::class,
            NonEmptyString::class,
            InterfaceString::class => $this->clarifyStringType($type),
            True_::class => new TrueType(),
            Void_::class => new VoidType(),
            Scalar::class => new ScalarType(),
            default => new MixedType(),
        };

        if ($defaultType instanceof MixedType || $this->typeComparator->compare($defaultType, $clarifyType)) {
            return $clarifyType;
        }

        return $defaultType;
    }

    /**
     * @return array<TypeInterface>
     */
    private function getMultipleTypes(
        TypeInterface $defaultType,
        Compound|Intersection $type,
        ExtendedReflectionInterface $reflector
    ): array {
        $types = [];
        foreach ($type->getIterator() as $subType) {
            $types[] = $this->clarifyType($defaultType, $subType, $reflector);
        }

        return $types;
    }

    private function clarifyArrayType(AbstractList $type, ExtendedReflectionInterface $reflector): TypeInterface
    {
        return new ArrayType(
            $this->clarifyType(new ArrayKeyType(), $type->getKeyType(), $reflector),
            $this->clarifyType(new MixedType(), $type->getValueType(), $reflector),
        );
    }

    private function clarifyIntegerType(Integer $type): TypeInterface
    {
        if ($type instanceof IntegerRange) {
            $min = $type->getMinValue() ? (int)$type->getMinValue() : null;
            $max = $type->getMaxValue() ? (int)$type->getMaxValue() : null;
            return new IntType($min, $max);
        }

        if ($type instanceof PositiveInteger) {
            return new IntType(0);
        }

        if ($type instanceof NegativeInteger) {
            return new IntType(null, 0);
        }

        return new IntType();
    }

    public function clarifyStringType(String_|InterfaceString|ClassString $type): TypeInterface
    {
        $restriction = match ($type::class) {
            ClassString::class => StringRestrictionEnum::CLASS_NAME,
            LiteralString::class => StringRestrictionEnum::LITERAL,
            NonEmptyLowercaseString::class => StringRestrictionEnum::NON_EMPTY_LOWERCASE,
            TraitString::class => StringRestrictionEnum::TRAIT_NAME,
            LowercaseString::class => StringRestrictionEnum::LOWERCASE,
            CallableString::class => StringRestrictionEnum::CALLABLE,
            HtmlEscapedString::class => StringRestrictionEnum::HTML_ESCAPED,
            NumericString::class => StringRestrictionEnum::NUMERIC,
            NonEmptyString::class => StringRestrictionEnum::NON_EMPTY,
            InterfaceString::class => StringRestrictionEnum::INTERFACE_NAME,
            default => StringRestrictionEnum::NONE,
        };

        return new StringType($restriction);
    }

    private function clarifyObjectType(
        Object_|Self_|Static_ $type,
        ExtendedReflectionInterface $reflector
    ): TypeInterface {
        if ($type instanceof Self_ || $type instanceof Static_) {
            if ($reflector instanceof ExtendedParameterReflection) {
                return new NamedObjectType($reflector->getDeclaringMethod()->getDeclaringClass()->getName());
            }
            if ($reflector instanceof ExtendedPropertyReflection) {
                return new NamedObjectType($reflector->getDeclaringClass()->getName());
            }
            return new ObjectType();
        }

        if ($type->getFqsen() !== null) {
            return new NamedObjectType($this->fullyQualifiedClassName($type->getFqsen()));
        }

        return new ObjectType();
    }
}
