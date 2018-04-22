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

namespace Acme\App\Infrastructure\Persistence\Doctrine;

use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryInterface;
use Acme\PhpExtension\Helper\ClassHelper;
use Doctrine\ORM\AbstractQuery;

final class DqlQueryBuilder implements DqlQueryBuilderInterface
{
    /**
     * @var array
     */
    private $filters = [];

    /**
     * @var int
     */
    private $hydrationMode = AbstractQuery::HYDRATE_OBJECT;

    public function build(): QueryInterface
    {
        $dqlQuery = new DqlQuery($this->filters);
        $dqlQuery->setHydrationMode($this->hydrationMode);

        return $dqlQuery;
    }

    public function create(string $entityName, string $alias = null, string $indexBy = null): DqlQueryBuilderInterface
    {
        $alias = $alias ?? ClassHelper::extractCanonicalClassName($entityName);

        $this->reset();

        return $this->select($alias)->from($entityName, $alias, $indexBy)->setMaxResults(self::DEFAULT_MAX_RESULTS);
    }

    public function setParameter($key, $value, $type = null): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function setMaxResults(int $maxResults): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function select(string ...$select): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function distinct(bool $flag = true): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function addSelect(string ...$select): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function delete(string $delete = null, string $alias = null): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function update(string $update = null, string $alias = null): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function from(string $from, string $alias, string $indexBy = null): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function indexBy(string $alias, string $indexBy): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function join(
        string $join,
        string $alias,
        string $conditionType = null,
        string $condition = null,
        string $indexBy = null
    ): DqlQueryBuilderInterface {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function innerJoin(
        string $join,
        string $alias,
        string $conditionType = null,
        string $condition = null,
        string $indexBy = null
    ): DqlQueryBuilderInterface {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function leftJoin(
        string $join,
        string $alias,
        string $conditionType = null,
        string $condition = null,
        string $indexBy = null
    ): DqlQueryBuilderInterface {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function where(string $predicates): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function andWhere(string $predicates): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function orWhere(string $predicates): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function groupBy(string $groupBy): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function addGroupBy(string $groupBy): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function having(string $having): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function andHaving(string $having): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function orHaving(string $having): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function orderBy(string $sort, string $order = null): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function addOrderBy(string $sort, string $order = null): DqlQueryBuilderInterface
    {
        return $this->addFilter(__METHOD__, \func_get_args());
    }

    public function useScalarHydration(): DqlQueryBuilderInterface
    {
        $this->hydrationMode = AbstractQuery::HYDRATE_SCALAR;

        return $this;
    }

    private function addFilter(string $methodName, array $args): DqlQueryBuilderInterface
    {
        $this->filters[] = [$methodName, $args];

        return $this;
    }

    private function reset(): void
    {
        $this->filters = [];
        $this->hydrationMode = AbstractQuery::HYDRATE_OBJECT;
    }
}
