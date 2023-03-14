<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Definitions\DTO;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @template-covariant TValue of object
 * @implements \IteratorAggregate<TValue>
 */
final class Collection implements \IteratorAggregate, \Countable
{
    /** @var array<array-key, TValue> */
    private readonly array $items;

    /**
     * @param list<TValue> $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @return \Generator<array-key, TValue>
     */
    public function getIterator(): \Generator
    {
        return yield from $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function has(string $className): bool
    {
        foreach ($this->items as $item) {
            if ($item instanceof $className) {
                return true;
            }
        }

        return false;
    }

    /**
     * @template TClass
     * @param class-string<TClass> $className
     * @return self<TClass>
     */
    public function type(string $className): self
    {
        /** @var self<TClass> */
        return $this->filter(
            static fn (object $item): bool => $item instanceof $className
        );
    }

    /**
     * @psalm-mutation-free
     * @param pure-callable(TValue): bool $filter
     * @return self<TValue>
     */
    public function filter(callable $filter): self
    {
        $values = array_values(
            array_filter($this->items, $filter)
        );

        return new self($values);
    }

    /**
     * @param callable(mixed, mixed): int $sort
     * @return self<TValue>
     */
    public function sort(callable $sort): self
    {
        $values = $this->items;
        usort($values, $sort);

        return new self($values);
    }

    /**
     * @return TValue|null
     */
    public function first(): ?object
    {
        return $this->items[0] ?? null;
    }

    /**
     * @return array<array-key, TValue>
     */
    public function all(): array
    {
        return $this->items;
    }

    public function toArray(): array
    {
        return array_map(
            function ($item) {
                if ($item instanceof Arrayable) {
                    return $item->toArray();
                } else {
                    return $item;
                }
            },
            $this->items
        );
    }
}
