<?php

namespace Tochka\Hydrator;

use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use phpDocumentor\Reflection\DocBlockFactory as ReflectionDocBlockFactory;
use Spiral\Attributes\AnnotationReader as SpiralAnnotationReader;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Composite\MergeReader;
use Tochka\Hydrator\Casters\BenSampoEnumCaster;
use Tochka\Hydrator\Casters\CarbonCaster;
use Tochka\Hydrator\Casters\EnumCaster;
use Tochka\Hydrator\Contracts\AnnotationReaderInterface;
use Tochka\Hydrator\Contracts\CasterRegistryInterface;
use Tochka\Hydrator\Contracts\ClassDefinitionsRegistryInterface;
use Tochka\Hydrator\Contracts\DefinitionParserInterface;
use Tochka\Hydrator\Contracts\ExtendedReflectionFactoryInterface;
use Tochka\Hydrator\Contracts\ExtractorInterface;
use Tochka\Hydrator\Contracts\TypeDefinitionFactoryInterface;
use Tochka\Hydrator\ExtendedReflection\ExtendedReflectionFactory;
use Tochka\Hydrator\ExtendedReflection\ExtendedTypeFactory;
use Tochka\Hydrator\ExtendedReflection\TypeFactories\DocBlockTypeFactoryMiddleware;
use Tochka\Hydrator\ExtendedReflection\TypeFactories\ReflectionTypeFactoryMiddleware;
use Tochka\Hydrator\Extractors\ArrayExtractor;
use Tochka\Hydrator\Extractors\MixedExtractor;
use Tochka\Hydrator\Extractors\ObjectExtractor;
use Tochka\Hydrator\Extractors\StringExtractor;
use Tochka\Hydrator\Extractors\StrongExtractor;
use Tochka\Hydrator\Support\AnnotationReader;
use Tochka\Hydrator\Support\CasterRegistry;
use Tochka\Hydrator\Support\TypeDefinitionFactory;

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

        $this->app->singleton(TypeDefinitionFactoryInterface::class, TypeDefinitionFactory::class);
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

        $this->app->singleton(
            CasterRegistryInterface::class,
            function (Container $container): CasterRegistry {
                $registry = new CasterRegistry($container);

                if (class_exists('\BenSampo\Enum\Enum')) {
                    $registry->addCaster(new BenSampoEnumCaster());
                }
                if (class_exists('\Carbon\Carbon')) {
                    $registry->addCaster(new CarbonCaster());
                }
                if (function_exists('enum_exists')) {
                    $registry->addCaster(new EnumCaster());
                }

                return $registry;
            }
        );

        $this->app->singleton(ClassDefinitionsRegistryInterface::class, ClassDefinitionsRegistry::class);
        $this->app->singleton(DefinitionParserInterface::class, DefinitionParser::class);
        $this->app->singleton(ExtractorInterface::class, function (Container $container): Extractor {
            /** @var Extractor $extractor */
            $extractor = $container->make(Extractor::class);
            $extractor->registerValueExtractor(new MixedExtractor());
            $extractor->registerValueExtractor(new StringExtractor());
            $extractor->registerValueExtractor(new StrongExtractor());
            $extractor->registerValueExtractor(new ArrayExtractor());
            $extractor->registerValueExtractor(new ObjectExtractor());

            return $extractor;
        });
    }

    private function registerIgnoredAnnotations(): void
    {
        foreach (self::IGNORED_ANNOTATIONS as $annotationName) {
            DoctrineAnnotationReader::addGlobalIgnoredName($annotationName);
        }
    }
}
