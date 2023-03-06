<?php

declare(strict_types=1);

namespace Tochka\Hydrator\ExtendedReflection;

use phpDocumentor\Reflection\DocBlockFactory;
use Tochka\Hydrator\Contracts\AnnotationReaderInterface;
use Tochka\Hydrator\Contracts\ExtendedReflectionFactoryInterface;
use Tochka\Hydrator\ExtendedReflection\Reflectors\ExtendedClassReflection;
use Tochka\Hydrator\ExtendedReflection\Reflectors\ExtendedMethodReflection;
use Tochka\Hydrator\ExtendedReflection\Reflectors\ExtendedParameterReflection;
use Tochka\Hydrator\ExtendedReflection\Reflectors\ExtendedPropertyReflection;

class ExtendedReflectionFactory implements ExtendedReflectionFactoryInterface
{
    /** @var array<string, ExtendedReflectionInterface> */
    private array $reflections = [];
    private AnnotationReaderInterface $annotationReader;
    private DocBlockFactory $docBlockFactory;
    private ExtendedTypeFactory $typeFactory;

    public function __construct(
        AnnotationReaderInterface $annotationReader,
        DocBlockFactory $docBlockFactory,
        ExtendedTypeFactory $typeFactory,
    ) {
        $this->annotationReader = $annotationReader;
        $this->docBlockFactory = $docBlockFactory;
        $this->typeFactory = $typeFactory;
    }

    /**
     * @template T of ExtendedReflectionInterface
     * @param callable(): T $make
     * @return T
     */
    private function getOrMake(string $key, callable $make): ExtendedReflectionInterface
    {
        if (!array_key_exists($key, $this->reflections)) {
            $this->reflections[$key] = $make();
        }

        /** @var T */
        return $this->reflections[$key];
    }

    /**
     * @throws \ReflectionException
     */
    public function makeForClass(string $className): ExtendedClassReflection
    {
        return $this->getOrMake(
            $this->getKeyForClass($className),
            function () use ($className): ExtendedClassReflection {
                return new ExtendedClassReflection(
                    new \ReflectionClass($className),
                    $this->annotationReader,
                    $this->docBlockFactory,
                    $this->typeFactory,
                );
            }
        );
    }

    /**
     * @throws \ReflectionException
     */
    public function makeForMethod(string $className, string $methodName): ExtendedMethodReflection
    {
        return $this->getOrMake(
            $this->getKeyForMethod($className, $methodName),
            function () use ($className, $methodName): ExtendedMethodReflection {
                return new ExtendedMethodReflection(
                    $this->makeForClass($className),
                    new \ReflectionMethod($className, $methodName),
                    $this->annotationReader,
                    $this->docBlockFactory,
                    $this->typeFactory,
                );
            }
        );
    }

    /**
     * @throws \ReflectionException
     */
    public function makeForProperty(string $className, string $propertyName): ExtendedPropertyReflection
    {
        return $this->getOrMake(
            $this->getKeyForProperty($className, $propertyName),
            function () use ($className, $propertyName): ExtendedPropertyReflection {
                return new ExtendedPropertyReflection(
                    $this->makeForClass($className),
                    new \ReflectionProperty($className, $propertyName),
                    $this->annotationReader,
                    $this->docBlockFactory,
                    $this->typeFactory,
                );
            }
        );
    }

    /**
     * @throws \ReflectionException
     */
    public function makeForParameter(
        string $className,
        string $methodName,
        string $parameterName
    ): ExtendedParameterReflection {
        return $this->getOrMake(
            $this->getKeyForParameter($methodName, $parameterName, $className),
            function () use ($className, $methodName, $parameterName): ExtendedParameterReflection {
                return new ExtendedParameterReflection(
                    $this->makeForMethod($className, $methodName),
                    new \ReflectionParameter([$className, $methodName], $parameterName),
                    $this->annotationReader,
                    $this->docBlockFactory,
                    $this->typeFactory,
                );
            }
        );
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
}
