<?php

declare(strict_types=1);

namespace Tochka\Hydrator\TypeSystem\Types;

use Tochka\Hydrator\TypeSystem\DTO\Parameter;
use Tochka\Hydrator\TypeSystem\TypeInterface;

/**
 * @psalm-api
 * @psalm-immutable
 * @template-covariant TReturn
 * @implements TypeInterface<callable(): TReturn>
 */
final class CallableType implements TypeInterface
{
    /**
     * @var list<Parameter>
     */
    public readonly array $parameters;

    /**
     * @param list<TypeInterface|Parameter> $parameters
     * @param TypeInterface<TReturn>|null $returnType
     */
    public function __construct(
        array $parameters = [],
        public readonly ?TypeInterface $returnType = null,
    ) {
        $this->parameters = array_map(
            static fn (TypeInterface|Parameter $parameter): Parameter => $parameter instanceof TypeInterface
                ? new Parameter($parameter)
                : $parameter,
            $parameters,
        );
    }
}
