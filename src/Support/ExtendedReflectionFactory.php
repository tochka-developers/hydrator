<?php

namespace Tochka\Hydrator\Support;

use phpDocumentor\Reflection\DocBlockFactory as ReflectionDocBlock;
use Tochka\Hydrator\Contracts\AnnotationReaderInterface;
use Tochka\Hydrator\Contracts\ExtendedReflectionFactoryInterface;
use Tochka\Hydrator\Exceptions\InternalHydratorException;

/**
 * @psalm-api
 */
class ExtendedReflectionFactory implements ExtendedReflectionFactoryInterface
{
    /** @var array<string, ExtendedReflection> */
    private array $docBlocks = [];
    private AnnotationReaderInterface $annotationReader;
    private ReflectionDocBlock $docBlockFactory;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(ReflectionDocBlock $docBlockFactory, AnnotationReaderInterface $annotationReader)
    {
        $this->docBlockFactory = $docBlockFactory;
        $this->annotationReader = $annotationReader;
    }

    public function make(\Reflector $reflector): ExtendedReflection
    {
        $callable = static function () use ($reflector): \Reflector {
            return $reflector;
        };

        if ($reflector instanceof \ReflectionClass) {
            return $this->getOrMakeFromReflection(
                $this->getKeyForClass($reflector->getName()),
                $callable
            );
        }

        if ($reflector instanceof \ReflectionMethod) {
            return $this->getOrMakeFromReflection(
                $this->getKeyForMethod($reflector->getDeclaringClass()->getName(), $reflector->getName()),
                $callable
            );
        }

        if ($reflector instanceof \ReflectionProperty) {
            return $this->getOrMakeFromReflection(
                $this->getKeyForProperty($reflector->getDeclaringClass()->getName(), $reflector->getName()),
                $callable
            );
        }

        if ($reflector instanceof \ReflectionFunction) {
            return $this->getOrMakeFromReflection(
                $this->getKeyForFunction($reflector->getName()),
                $callable
            );
        }

        if ($reflector instanceof \ReflectionParameter) {
            return $this->getOrMakeFromReflection(
                $this->getKeyForParameter(
                    $reflector->getDeclaringFunction()->getName(),
                    $reflector->getName(),
                    $reflector->getDeclaringClass()?->getName()
                ),
                $callable
            );
        }
        if ($reflector instanceof \ReflectionClassConstant) {
            return $this->getOrMakeFromReflection(
                $this->getKeyForClassConstant($reflector->getDeclaringClass()->getName(), $reflector->getName()),
                $callable
            );
        }

        throw new InternalHydratorException(sprintf('Unsupported reflector [%s]', get_class($reflector)));
    }

    public function makeForClass(string $className): ExtendedReflection
    {
        try {
            return $this->getOrMakeFromReflection(
                $this->getKeyForClass($className),
                function () use ($className) {
                    return new \ReflectionClass($className);
                }
            );
        } catch (\ReflectionException $e) {
            throw new InternalHydratorException(sprintf('Error while make reflection for class [%s]', $className), $e);
        }
    }

    public function makeForMethod(string $className, string $methodName): ExtendedReflection
    {
        try {
            return $this->getOrMakeFromReflection(
                $this->getKeyForMethod($className, $methodName),
                function () use ($className, $methodName) {
                    return new \ReflectionMethod($className, $methodName);
                }
            );
        } catch (\ReflectionException $e) {
            throw new InternalHydratorException(
                sprintf('Error while make reflection for method [%s:%s]', $className, $methodName), $e
            );
        }
    }

    public function makeForProperty(string $className, string $propertyName): ExtendedReflection
    {
        try {
            return $this->getOrMakeFromReflection(
                $this->getKeyForProperty($className, $propertyName),
                function () use ($className, $propertyName) {
                    return new \ReflectionProperty($className, $propertyName);
                }
            );
        } catch (\ReflectionException $e) {
            throw new InternalHydratorException(
                sprintf('Error while make reflection for property [%s:%s]', $className, $propertyName), $e
            );
        }
    }

    public function makeForParameter(string $className, string $methodName, string $parameterName): ExtendedReflection
    {
        try {
            return $this->getOrMakeFromReflection(
                $this->getKeyForParameter($className, $methodName, $parameterName),
                function () use ($className, $methodName, $parameterName) {
                    return new \ReflectionParameter([$className, $methodName], $parameterName);
                }
            );
        } catch (\ReflectionException $e) {
            throw new InternalHydratorException(
                sprintf(
                    'Error while make reflection for parameter [%s:%s<%s>]',
                    $className,
                    $methodName,
                    $parameterName
                ), $e
            );
        }
    }

    /**
     * @param string $key
     * @param callable(): \Reflector $reflectorCallable
     * @return ExtendedReflection
     */
    private function getOrMakeFromReflection(string $key, callable $reflectorCallable): ExtendedReflection
    {
        if (!array_key_exists($key, $this->docBlocks)) {
            $this->docBlocks[$key] = new ExtendedReflection(
                $reflectorCallable(),
                $this->annotationReader,
                $this->docBlockFactory
            );
        }

        return $this->docBlocks[$key];
    }

    private function getKeyForClass(string $className): string
    {
        return sprintf('class:%s', $className);
    }

    private function getKeyForMethod(string $className, string $methodName): string
    {
        return sprintf('method:%s:%s', $className, $methodName);
    }

    private function getKeyForProperty(string $className, string $propertyName): string
    {
        return sprintf('property:%s:%s', $className, $propertyName);
    }

    private function getKeyForFunction(string $functionName): string
    {
        return sprintf('function:%s', $functionName);
    }

    private function getKeyForParameter(string $methodName, string $parameterName, ?string $className = null): string
    {
        if ($className === null) {
            $className = '$global$';
        }
        return sprintf('parameter:%s:%s:%s', $className, $methodName, $parameterName);
    }

    private function getKeyForClassConstant(string $className, string $constantName): string
    {
        return sprintf('classConstant:%s:%s', $className, $constantName);
    }
}
