<?php

declare(strict_types=1);

namespace Tochka\Hydrator;

use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use phpDocumentor\Reflection\DocBlockFactory as ReflectionDocBlockFactory;
use Spiral\Attributes\AnnotationReader as SpiralAnnotationReader;
use Spiral\Attributes\AttributeReader as SpiralAttributeReader;
use Spiral\Attributes\Composite\MergeReader;
use Tochka\Hydrator\Contracts\ClassDefinitionParserInterface;
use Tochka\Hydrator\Contracts\ClassDefinitionsRegistryInterface;
use Tochka\Hydrator\Contracts\ExtractorInterface;
use Tochka\Hydrator\Contracts\HydratorInterface;
use Tochka\Hydrator\Contracts\MethodDefinitionParserInterface;
use Tochka\Hydrator\Definitions\ClassDefinitionParser;
use Tochka\Hydrator\Definitions\ClassDefinitionsRegistry;
use Tochka\Hydrator\Definitions\MethodDefinitionParser;
use Tochka\Hydrator\Extractors\ArrayExtractor;
use Tochka\Hydrator\Extractors\BenSampoEnumExtractor;
use Tochka\Hydrator\Extractors\CarbonExtractor;
use Tochka\Hydrator\Extractors\DateTimeExtractor;
use Tochka\Hydrator\Extractors\DIExtractor;
use Tochka\Hydrator\Extractors\EnumExtractor;
use Tochka\Hydrator\Extractors\ExtractByExtractor;
use Tochka\Hydrator\Extractors\NamedObjectExtractor;
use Tochka\Hydrator\Extractors\NullExtractor;
use Tochka\Hydrator\Extractors\ObjectExtractor;
use Tochka\Hydrator\Extractors\StringExtractor;
use Tochka\Hydrator\Extractors\StrongScalarExtractor;
use Tochka\Hydrator\Extractors\UnionExtractor;
use Tochka\TypeParser\AttributeReader;
use Tochka\TypeParser\Contracts\AttributeReaderInterface;
use Tochka\TypeParser\Contracts\ExtendedReflectionFactoryInterface;
use Tochka\TypeParser\ExtendedReflectionFactory;
use Tochka\TypeParser\ExtendedTypeFactory;
use Tochka\TypeParser\TypeFactories\DocBlockTypeFactoryMiddleware;
use Tochka\TypeParser\TypeFactories\ReflectionTypeFactoryMiddleware;

/**
 * @psalm-api
 */
class HydratorServiceProvider extends ServiceProvider
{
    private const IGNORED_ANNOTATIONS = [
        'apiGroupName',
        'apiIgnoreMethod',
        'apiName',
        'apiDescription',
        'apiNote',
        'apiWarning',
        'apiParam',
        'apiRequestExample',
        'apiResponseExample',
        'apiReturn',
        'apiTag',
        'apiEnum',
        'apiObject',
        'mixin',
    ];

    public function register(): void
    {
        $this->registerIgnoredAnnotations();

        $this->app->singleton(AttributeReaderInterface::class, function (): AttributeReader {
            return new AttributeReader(
                new MergeReader(
                    [
                        new SpiralAnnotationReader(),
                        new SpiralAttributeReader(),
                    ]
                )
            );
        });

        $this->app->when(ExtendedTypeFactory::class)
            ->needs('$typeFactoryMiddleware')
            ->give(
                [
                    ReflectionTypeFactoryMiddleware::class,
                    DocBlockTypeFactoryMiddleware::class
                ]
            );

        $this->app->singleton(
            ExtendedReflectionFactoryInterface::class,
            function (Container $container): ExtendedReflectionFactoryInterface {
                /** @var ExtendedReflectionFactory */
                return $container->make(
                    ExtendedReflectionFactory::class,
                    ['docBlockFactory' => ReflectionDocBlockFactory::createInstance()]
                );
            }
        );

        $this->app->singleton(ClassDefinitionsRegistryInterface::class, ClassDefinitionsRegistry::class);
        $this->app->singleton(ClassDefinitionParserInterface::class, ClassDefinitionParser::class);
        $this->app->singleton(MethodDefinitionParserInterface::class, MethodDefinitionParser::class);
        $this->app->singleton(ExtractorInterface::class, Extractor::class);
        $this->app->singleton(HydratorInterface::class, Hydrator::class);

        $this->app->afterResolving(ExtractorInterface::class, function (ExtractorInterface $extractor) {
            $extractor->registerExtractor(DIExtractor::class);
            $extractor->registerExtractor(ExtractByExtractor::class);
            $extractor->registerExtractor(NullExtractor::class);
            $extractor->registerExtractor(UnionExtractor::class);
            $extractor->registerExtractor(BenSampoEnumExtractor::class);
            $extractor->registerExtractor(EnumExtractor::class);
            $extractor->registerExtractor(CarbonExtractor::class);
            $extractor->registerExtractor(DateTimeExtractor::class);
            $extractor->registerExtractor(StrongScalarExtractor::class);
            $extractor->registerExtractor(StringExtractor::class);
            $extractor->registerExtractor(ArrayExtractor::class);
            $extractor->registerExtractor(NamedObjectExtractor::class);
            $extractor->registerExtractor(ObjectExtractor::class);
        });

//        $this->app->afterResolving(HydratorInterface::class, function (HydratorInterface $hydrator) {
//        });
    }

    private function registerIgnoredAnnotations(): void
    {
        foreach (self::IGNORED_ANNOTATIONS as $annotationName) {
            DoctrineAnnotationReader::addGlobalIgnoredName($annotationName);
        }
    }
}
