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

use Acme\App\Core\Port\Persistence\Exception\QueryServiceIsNotCallableException;
use Acme\App\Core\Port\Persistence\Exception\UnableToHandleQueryException;

/**
 * This is the query service that will be used throughout the application, it's the entry point for querying.
 * It will receive a query object and route it to one of the underlying query services,
 * which is configured to handle that specific type of query objects.
 */
final class QueryServiceRouter implements QueryServiceRouterInterface
{
    /**
     * @var QueryServiceInterface|callable[]
     */
    private $queryServiceList = [];

    public function __construct(QueryServiceInterface ...$queryServiceList)
    {
        foreach ($queryServiceList as $queryService) {
            $this->addQueryService($queryService);
        }
    }

    public function query(QueryInterface $query): ResultCollectionInterface
    {
        return $this->getQueryServiceFor($query)($query);
    }

    private function addQueryService(QueryServiceInterface $queryService): void
    {
        if (!\is_callable($queryService)) {
            throw new QueryServiceIsNotCallableException($queryService);
        }

        $this->queryServiceList[$queryService->canHandle()] = $queryService;
    }

    private function getQueryServiceFor(QueryInterface $query): QueryServiceInterface
    {
        if (!$this->canHandle($query)) {
            throw new UnableToHandleQueryException($query);
        }

        return $this->queryServiceList[\get_class($query)];
    }

    private function canHandle(QueryInterface $query): bool
    {
        return isset($this->queryServiceList[\get_class($query)]);
    }
}
