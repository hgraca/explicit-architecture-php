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

namespace Acme\App\Core\Port\Persistence\DQL;

use Acme\App\Core\Port\Persistence\QueryBuilderInterface;

interface DqlQueryBuilderInterface extends QueryBuilderInterface
{
    public function create(string $entityName, string $alias = null, string $indexBy = null): self;

    /**
     * Sets a query parameter for the query being constructed.
     *
     * <code>
     *     $qb = $dqlQuery()
     *         ->select('u')
     *         ->from('User', 'u')
     *         ->where('u.id = :user_id')
     *         ->setParameter('user_id', 1);
     * </code>
     *
     * @param string|int      $key   the parameter position or name
     * @param mixed           $value the parameter value
     * @param string|int|null $type  PDO::PARAM_* or \Doctrine\DBAL\Types\Type::* constant
     */
    public function setParameter($key, $value, $type = null): self;

    /**
     * Sets the maximum number of results to retrieve (the "limit").
     *
     * @param int $maxResults the maximum number of results to retrieve
     */
    public function setMaxResults(int $maxResults): self;

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * <code>
     *     $qb = $dqlQuery()
     *         ->select('u', 'p')
     *         ->from('User', 'u')
     *         ->leftJoin('u.Phonenumbers', 'p');
     * </code>
     *
     * @param string $select the selection expressions
     */
    public function select(string ...$select): self;

    /**
     * Adds a DISTINCT flag to this query.
     *
     * <code>
     *     $qb = $dqlQuery()
     *         ->select('u')
     *         ->distinct()
     *         ->from('User', 'u');
     * </code>
     */
    public function distinct(bool $flag = true): self;

    /**
     * Adds an item that is to be returned in the query result.
     *
     * <code>
     *     $qb = $dqlQuery()
     *         ->select('u')
     *         ->addSelect('p')
     *         ->from('User', 'u')
     *         ->leftJoin('u.Phonenumbers', 'p');
     * </code>
     *
     * @param string $select the selection expression
     */
    public function addSelect(string ...$select): self;

    /**
     * Turns the query being built into a bulk delete query that ranges over
     * a certain entity type.
     *
     * <code>
     *     $qb = $dqlQuery()
     *         ->delete('User', 'u')
     *         ->where('u.id = :user_id')
     *         ->setParameter('user_id', 1);
     * </code>
     *
     * @param string $delete the class/type whose instances are subject to the deletion
     * @param string $alias  the class/type alias used in the constructed query
     */
    public function delete(string $delete = null, string $alias = null): self;

    /**
     * Turns the query being built into a bulk update query that ranges over
     * a certain entity type.
     *
     * <code>
     *     $qb = $dqlQuery()
     *         ->update('User', 'u')
     *         ->set('u.password', '?1')
     *         ->where('u.id = ?2');
     * </code>
     *
     * @param string $update the class/type whose instances are subject to the update
     * @param string $alias  the class/type alias used in the constructed query
     */
    public function update(string $update = null, string $alias = null): self;

    /**
     * Creates and adds a query root corresponding to the entity identified by the given alias,
     * forming a cartesian product with any existing query roots.
     *
     * <code>
     *     $qb = $dqlQuery()
     *         ->select('u')
     *         ->from('User', 'u');
     * </code>
     *
     * @param string $from    the class name
     * @param string $alias   the alias of the class
     * @param string $indexBy the index for the from
     */
    public function from(string $from, string $alias, string $indexBy = null): self;

    /**
     * Updates a query root corresponding to an entity setting its index by. This method is intended to be used with
     * EntityRepository->createQueryBuilder(), which creates the initial FROM clause and do not allow you to update it
     * setting an index by.
     *
     * <code>
     *     $qb = $userRepository->createQueryBuilder('u')
     *         ->indexBy('u', 'u.id');
     *
     *     // Is equivalent to...
     *
     *     $qb = $dqlQuery()
     *         ->select('u')
     *         ->from('User', 'u', 'u.id');
     * </code>
     *
     * @param string $alias   the root alias of the class
     * @param string $indexBy The index for the from.*
     */
    public function indexBy(string $alias, string $indexBy): self;

    /**
     * Creates and adds a join over an entity association to the query.
     *
     * The entities in the joined association will be fetched as part of the query
     * result if the alias used for the joined association is placed in the select
     * expressions.
     *
     * <code>
     *     $qb = $dqlQuery()
     *         ->select('u')
     *         ->from('User', 'u')
     *         ->join('u.Phonenumbers', 'p', Expr\Join::WITH, 'p.is_primary = 1');
     * </code>
     *
     * @param string      $join          the relationship to join
     * @param string      $alias         the alias of the join
     * @param string|null $conditionType The condition type constant. Either ON or WITH.
     * @param string|null $condition     the condition for the join
     * @param string|null $indexBy       the index for the join
     */
    public function join(
        string $join,
        string $alias,
        string $conditionType = null,
        string $condition = null,
        string $indexBy = null
    ): self;

    /**
     * Creates and adds a join over an entity association to the query.
     *
     * The entities in the joined association will be fetched as part of the query
     * result if the alias used for the joined association is placed in the select
     * expressions.
     *
     *     [php]
     *     $qb = $dqlQuery()
     *         ->select('u')
     *         ->from('User', 'u')
     *         ->innerJoin('u.Phonenumbers', 'p', Expr\Join::WITH, 'p.is_primary = 1');
     *
     * @param string      $join          the relationship to join
     * @param string      $alias         the alias of the join
     * @param string|null $conditionType The condition type constant. Either ON or WITH.
     * @param string|null $condition     the condition for the join
     * @param string|null $indexBy       the index for the join
     */
    public function innerJoin(
        string $join,
        string $alias,
        string $conditionType = null,
        string $condition = null,
        string $indexBy = null
    ): self;

    /**
     * Creates and adds a left join over an entity association to the query.
     *
     * The entities in the joined association will be fetched as part of the query
     * result if the alias used for the joined association is placed in the select
     * expressions.
     *
     * <code>
     *     $qb = $dqlQuery()
     *         ->select('u')
     *         ->from('User', 'u')
     *         ->leftJoin('u.Phonenumbers', 'p', Expr\Join::WITH, 'p.is_primary = 1');
     * </code>
     *
     * @param string      $join          the relationship to join
     * @param string      $alias         the alias of the join
     * @param string|null $conditionType The condition type constant. Either ON or WITH.
     * @param string|null $condition     the condition for the join
     * @param string|null $indexBy       the index for the join
     */
    public function leftJoin(
        string $join,
        string $alias,
        string $conditionType = null,
        string $condition = null,
        string $indexBy = null
    ): self;

    /**
     * Specifies one or more restrictions to the query result.
     * Replaces any previously specified restrictions, if any.
     *
     * <code>
     *     $qb = $dqlQuery()
     *         ->select('u')
     *         ->from('User', 'u')
     *         ->where('u.id = ?');
     * </code>
     *
     * @param mixed $predicates the restriction predicates
     */
    public function where(string $predicates): self;

    /**
     * Adds one or more restrictions to the query results, forming a logical
     * conjunction with any previously specified restrictions.
     *
     * <code>
     *     $qb = $dqlQuery()
     *         ->select('u')
     *         ->from('User', 'u')
     *         ->where('u.username LIKE ?')
     *         ->andWhere('u.is_active = 1');
     * </code>
     *
     * @see where()
     */
    public function andWhere(string $predicates): self;

    /**
     * Adds one or more restrictions to the query results, forming a logical
     * disjunction with any previously specified restrictions.
     *
     * <code>
     *     $qb = $dqlQuery()
     *         ->select('u')
     *         ->from('User', 'u')
     *         ->where('u.id = 1')
     *         ->orWhere('u.id = 2');
     * </code>
     *
     * @see where()
     */
    public function orWhere(string $predicates): self;

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * <code>
     *     $qb = $dqlQuery()
     *         ->select('u')
     *         ->from('User', 'u')
     *         ->groupBy('u.id');
     * </code>
     *
     * @param string $groupBy the grouping expression
     */
    public function groupBy(string $groupBy): self;

    /**
     * Adds a grouping expression to the query.
     *
     * <code>
     *     $qb = $dqlQuery()
     *         ->select('u')
     *         ->from('User', 'u')
     *         ->groupBy('u.lastLogin')
     *         ->addGroupBy('u.createdAt');
     * </code>
     *
     * @param string $groupBy the grouping expression
     */
    public function addGroupBy(string $groupBy): self;

    /**
     * Specifies a restriction over the groups of the query.
     * Replaces any previous having restrictions, if any.
     *
     * @param string $having the restriction over the groups
     */
    public function having(string $having): self;

    /**
     * Adds a restriction over the groups of the query, forming a logical
     * conjunction with any existing having restrictions.
     *
     * @param string $having the restriction to append
     */
    public function andHaving(string $having): self;

    /**
     * Adds a restriction over the groups of the query, forming a logical
     * disjunction with any existing having restrictions.
     *
     * @param string $having the restriction to add
     */
    public function orHaving(string $having): self;

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $sort  the ordering expression
     * @param string $order the ordering direction (DESC|ASC)
     */
    public function orderBy(string $sort, string $order = null): self;

    /**
     * Adds an ordering to the query results.
     *
     * @param string $sort  the ordering expression
     * @param string $order the ordering direction (DESC|ASC)
     */
    public function addOrderBy(string $sort, string $order = null): self;

    public function useScalarHydration(): self;
}
