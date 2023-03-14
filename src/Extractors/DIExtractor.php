<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Tochka\Hydrator\Attributes\DI;
use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\ContainerValueException;
use Tochka\Hydrator\TypeSystem\Types\NamedObjectType;

final class DIExtractor implements ValueExtractorInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    public function extract(FromContainer $from, ToContainer $to, Context $context, callable $next): mixed
    {
        /**
         * @psalm-ignore-var
         * @var DI|null $di
         */
        $di = $to->attributes->type(DI::class)->first();

        if ($di === null) {
            return $next($from, $to, $context);
        }

        if ($di->name !== null) {
            $containerItemName = $di->name;
        } elseif ($to->type instanceof NamedObjectType) {
            $containerItemName = $to->type->className;
        } else {
            throw new ContainerValueException('Undefined DI item name', $context);
        }

        try {
            return $this->container->get($containerItemName);
        } catch (ContainerExceptionInterface $e) {
            throw new ContainerValueException('Error while make value from DI', $context, $e);
        }
    }
}
