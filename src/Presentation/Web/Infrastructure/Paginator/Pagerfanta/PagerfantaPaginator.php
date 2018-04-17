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

namespace Acme\App\Presentation\Web\Infrastructure\Paginator\Pagerfanta;

use Acme\App\Presentation\Web\Core\Port\Paginator\PaginatorInterface;
use ArrayIterator;
use IteratorAggregate;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

final class PagerfantaPaginator implements PaginatorInterface, PagerfantaInterface, IteratorAggregate
{
    /**
     * @var Pagerfanta
     */
    private $pagerfanta;

    /**
     * @var array
     */
    private $data;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $maxItemsPerPage;

    public function __construct(
        array $data,
        int $page = self::DEFAULT_PAGE,
        int $maxItemsPerPage = self::DEFAULT_MAX_ITEMS_PER_PAGE
    ) {
        $this->data = $data;
        $this->page = $page;
        $this->maxItemsPerPage = $maxItemsPerPage;
    }

    public function getIterator(): ArrayIterator
    {
        $this->paginate($this->data);

        return $this->pagerfanta->getIterator();
    }

    public function count(): int
    {
        return $this->getIterator()->count();
    }

    public function totalCount(): int
    {
        $this->paginate($this->data);

        return $this->pagerfanta->count();
    }

    public function haveToPaginate(): bool
    {
        $this->paginate($this->data);

        return $this->pagerfanta->haveToPaginate();
    }

    public function getCurrentPage(): int
    {
        $this->paginate($this->data);

        return $this->pagerfanta->getCurrentPage();
    }

    public function getNbPages(): int
    {
        $this->paginate($this->data);

        return $this->pagerfanta->getNbPages();
    }

    public function hasPreviousPage(): bool
    {
        $this->paginate($this->data);

        return $this->pagerfanta->hasPreviousPage();
    }

    public function hasNextPage(): bool
    {
        $this->paginate($this->data);

        return $this->pagerfanta->hasNextPage();
    }

    public function getNextPage(): int
    {
        $this->paginate($this->data);

        return $this->pagerfanta->getNextPage();
    }

    public function getPreviousPage(): int
    {
        $this->paginate($this->data);

        return $this->pagerfanta->getPreviousPage();
    }

    public function setCurrentPage(int $page): void
    {
        $this->paginate($this->data);

        $this->pagerfanta->setCurrentPage($page);
    }

    private function paginate(array $data): void
    {
        if (!$this->pagerfanta) {
            $this->pagerfanta = new Pagerfanta(new ArrayAdapter($data));
            $this->pagerfanta->setMaxPerPage($this->calculateMaxPerPageForArray($data));
            $this->pagerfanta->setCurrentPage($this->page);
        }
    }

    private function calculateMaxPerPageForArray(array $result): int
    {
        if (\count($result) === 0) {
            return $this->maxItemsPerPage;
        }

        return min([\count($result), $this->maxItemsPerPage]);
    }
}
