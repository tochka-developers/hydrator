<?php

declare(strict_types=1);

namespace Tochka\Hydrator;

use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use phpDocumentor\Reflection\DocBlockFactory as ReflectionDocBlockFactory;
use Spiral\Attributes\AnnotationReader as SpiralAnnotationReader;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Composite\MergeReader;
use Tochka\Hydrator\Contracts\AnnotationReaderInterface;
use Tochka\Hydrator\Contracts\ClassDefinitionParserInterface;
use Tochka\Hydrator\Contracts\ClassDefinitionsRegistryInterface;
use Tochka\Hydrator\Contracts\ExtendedReflectionFactoryInterface;
use Tochka\Hydrator\Contracts\ExtractorInterface;
use Tochka\Hydrator\Contracts\HydratorInterface;
use Tochka\Hydrator\Contracts\MethodDefinitionParserInterface;
use Tochka\Hydrator\Definitions\ClassDefinitionParser;
use Tochka\Hydrator\Definitions\ClassDefinitionsRegistry;
use Tochka\Hydrator\Definitions\MethodDefinitionParser;
use Tochka\Hydrator\ExtendedReflection\AnnotationReader;
use Tochka\Hydrator\ExtendedReflection\ExtendedReflectionFactory;
use Tochka\Hydrator\ExtendedReflection\ExtendedTypeFactory;
use Tochka\Hydrator\ExtendedReflection\TypeFactories\DocBlockTypeFactoryMiddleware;
use Tochka\Hydrator\ExtendedReflection\TypeFactories\ReflectionTypeFactoryMiddleware;
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
use Tochka\Hydrator\Hydrators\ArrayHydrator;
use Tochka\Hydrator\Hydrators\BenSampoEnumHydrator;
use Tochka\Hydrator\Hydrators\CarbonHydrator;
use Tochka\Hydrator\Hydrators\EnumHydrator;
use Tochka\Hydrator\Hydrators\ObjectHydrator;

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

        $this->app->singleton(AnnotationReaderInterface::class, function (): AnnotationReaderInterface {
            return new AnnotationReader(
                new MergeReader(
                    [
                        new SpiralAnnotationReader(),
                        new AttributeReader(),
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

        $this->app->afterResolving(HydratorInterface::class, function (HydratorInterface $hydrator) {
            $hydrator->registerHydrator(BenSampoEnumHydrator::class);
            $hydrator->registerHydrator(EnumHydrator::class);
            $hydrator->registerHydrator(CarbonHydrator::class);
            $hydrator->registerHydrator(ArrayHydrator::class);
            $hydrator->registerHydrator(ObjectHydrator::class);
        });
    }

    private function registerIgnoredAnnotations(): void
    {
        foreach (self::IGNORED_ANNOTATIONS as $annotationName) {
            DoctrineAnnotationReader::addGlobalIgnoredName($annotationName);
        }
    }
}
