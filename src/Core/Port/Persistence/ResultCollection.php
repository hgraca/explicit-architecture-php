<?php

declare(strict_types=1);

/*
 * This file is part of the Explicit Architecture POC,
 * which is created on top of the Symfony Demo application.
 *
 * (c) Herberto Graça <herberto.graca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acme\App\Core\Port\Persistence;

use Acme\App\Core\Port\Persistence\Exception\CanOnlyHydrateFromArrayException;
use Acme\App\Core\Port\Persistence\Exception\EmptyQueryResultException;
use Acme\App\Core\Port\Persistence\Exception\NotConstructableFromArrayException;
use Acme\App\Core\Port\Persistence\Exception\NotUniqueQueryResultException;
use Acme\PhpExtension\ConstructableFromArrayInterface;
use ArrayIterator;
use Iterator;

/**
 * This class can be used by an adapter to wrap a result array, or the adapter can have its own collection
 * which must implement the ResultCollectionInterface.
 *
 * @internal this class should not be used for type-hinting, use the ResultCollectionInterface instead
 */
final class ResultCollection implements ResultCollectionInterface
{
    /**
     * @var array
     */
    private $itemList;

    public function __construct(array $itemList = [])
    {
        $this->itemList = $itemList;
    }

    public function getSingleResult()
    {
        $count = $this->count();

        if ($count > 1) {
            throw new NotUniqueQueryResultException();
        }

        if ($count === 0) {
            throw new EmptyQueryResultException();
        }

        return $this->getFirstElement();
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->itemList);
    }

    public function count(): int
    {
        return \count($this->itemList);
    }

    /**
     * @param string|ConstructableFromArrayInterface $fqcn
     */
    public function hydrateResultItemsAs(string $fqcn): ResultCollectionInterface
    {
        if (!is_subclass_of($fqcn, ConstructableFromArrayInterface::class)) {
            throw new NotConstructableFromArrayException($fqcn);
        }

        $item = reset($this->itemList);
        if (!\is_array($item)) { // we assume all items have the same type
            throw new CanOnlyHydrateFromArrayException($item);
        }

        $hydratedItemList = [];
        foreach ($this->itemList as $item) {
            $hydratedItemList[] = $fqcn::fromArray($item);
        }

        return new self($hydratedItemList);
    }

    /**
     * @param string|ConstructableFromArrayInterface $fqcn
     */
    public function hydrateSingleResultAs(string $fqcn)
    {
        if (!is_subclass_of($fqcn, ConstructableFromArrayInterface::class)) {
            throw new NotConstructableFromArrayException($fqcn);
        }

        $item = $this->getSingleResult();
        if (!\is_array($item)) { // we assume all items have the same type
            throw new CanOnlyHydrateFromArrayException($item);
        }

        return $fqcn::fromArray($item);
    }

    public function toArray(): array
    {
        return $this->itemList;
    }

    private function getFirstElement()
    {
        return \reset($this->itemList);
    }
}
