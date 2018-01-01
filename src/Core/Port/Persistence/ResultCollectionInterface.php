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

use Acme\App\Core\Port\Persistence\Exception\EmptyQueryResultException;
use Acme\App\Core\Port\Persistence\Exception\NotUniqueQueryResultException;
use Acme\PhpExtension\ConstructableFromArrayInterface;
use Countable;
use Iterator;
use IteratorAggregate;

/**
 * This is the interface that represents a result from a query.
 *
 * We can not rely on the internal iterator of the collection being at a certain
 * position unless you explicitly positioned it before, so we prefer iteration with
 * external iterators.
 */
interface ResultCollectionInterface extends Countable, IteratorAggregate
{
    /**
     * @throws EmptyQueryResultException
     * @throws NotUniqueQueryResultException
     *
     * @return mixed
     */
    public function getSingleResult();

    public function getIterator(): Iterator;

    public function count(): int;

    /**
     * @param string|ConstructableFromArrayInterface $fqcn
     */
    public function hydrateResultItemsAs(string $fqcn): self;

    /**
     * @param string|ConstructableFromArrayInterface $fqcn
     *
     * @return mixed
     */
    public function hydrateSingleResultAs(string $fqcn);

    public function toArray(): array;
}
