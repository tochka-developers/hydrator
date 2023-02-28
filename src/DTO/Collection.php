<?php

namespace Tochka\Hydrator\DTO;

/**
 * @template T of object
 * @extends \IteratorAggregate<T>
 */
class Collection implements \IteratorAggregate, \Countable
{
    /** @var array<array-key, T> */
    private array $items;

    /**
     * @param T ...$items
     */
    public function __construct(object ...$items)
    {
        $this->items = $items;
    }

    /**
     * @return \Generator<array-key, T>
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
     * @template TCollectionReturn
     * @param class-string<TCollectionReturn> $className
     * @return self<TCollectionReturn>
     */
    public function type(string $className): self
    {
        /** @var self<TCollectionReturn> */
        return $this->filter(
            static fn (object $item): bool => $item instanceof $className
        );
    }

    /**
     * @param callable(T): bool $filter
     * @return Collection<T>
     */
    public function filter(callable $filter): self
    {
        $values = array_values(
            array_filter($this->items, $filter)
        );

        return new self(...$values);
    }

    /**
     * @return T|null
     */
    public function first(): ?object
    {
        return $this->items[0] ?? null;
    }

    /**
     * @return array<array-key, T>
     */
    public function all(): array
    {
        return $this->items;
    }
}
