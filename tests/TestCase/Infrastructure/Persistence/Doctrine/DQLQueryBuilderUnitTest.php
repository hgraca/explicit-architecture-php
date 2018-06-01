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

namespace Acme\App\Test\TestCase\Infrastructure\Persistence\Doctrine;

use Acme\App\Core\Port\Persistence\QueryBuilderInterface;
use Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder;
use Acme\App\Test\Framework\AbstractUnitTest;
use Acme\PhpExtension\Helper\ReflectionHelper;
use Doctrine\ORM\AbstractQuery;

/**
 * @small
 */
final class DQLQueryBuilderUnitTest extends AbstractUnitTest
{
    /**
     * @var DqlQueryBuilder
     */
    private $queryBuilder;

    public function setUp(): void
    {
        $this->queryBuilder = new DqlQueryBuilder();
    }

    /**
     * This method is not tested here, by choice.
     * If we would make a unit test for it, we would end up testing the implementation and we don't want that.
     * We can safely assume that this method will be tested as a result of the integration tests made for the
     * repositories, as if this method does not function properly, those tests will fail.
     *
     * @test
     */
    public function build(): void
    {
        self::assertTrue(true);
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function create(): void
    {
        $this->queryBuilder->create('Some\Class\Full\Qualified\ClassName', 'alias', 'index');

        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                [
                    'Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::select',
                    ['alias'],
                ],
                [
                    'Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::from',
                    ['Some\Class\Full\Qualified\ClassName', 'alias', 'index'],
                ],
                [
                    'Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::setMaxResults',
                    [QueryBuilderInterface::DEFAULT_MAX_RESULTS],
                ],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function setParameter(): void
    {
        $this->queryBuilder->setParameter('key', 'value', 'type');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::setParameter', ['key', 'value', 'type']],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function setMaxResults(): void
    {
        $this->queryBuilder->setMaxResults(5);
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::setMaxResults', [5]],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function select(): void
    {
        $this->queryBuilder->select('select1', 'select2');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::select', ['select1', 'select2']],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function distinct(): void
    {
        $this->queryBuilder->distinct(false);
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::distinct', [false]],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function addSelect(): void
    {
        $this->queryBuilder->addSelect('select1', 'select2');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::addSelect', ['select1', 'select2']],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function delete(): void
    {
        $this->queryBuilder->delete('delete', 'alias');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::delete', ['delete', 'alias']],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function update(): void
    {
        $this->queryBuilder->update('update', 'alias');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::update', ['update', 'alias']],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function from(): void
    {
        $this->queryBuilder->from('from', 'alias', 'indexBy');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::from', ['from', 'alias', 'indexBy']],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function indexBy(): void
    {
        $this->queryBuilder->indexBy('alias', 'indexBy');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::indexBy', ['alias', 'indexBy']],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function join(): void
    {
        $this->queryBuilder->join('join', 'alias', 'conditionType', 'condition', 'indexBy');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                [
                    'Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::join',
                    ['join', 'alias', 'conditionType', 'condition', 'indexBy'],
                ],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function innerJoin(): void
    {
        $this->queryBuilder->innerJoin('join', 'alias', 'conditionType', 'condition', 'indexBy');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                [
                    'Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::innerJoin',
                    ['join', 'alias', 'conditionType', 'condition', 'indexBy'],
                ],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function leftJoin(): void
    {
        $this->queryBuilder->leftJoin('join', 'alias', 'conditionType', 'condition', 'indexBy');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                [
                    'Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::leftJoin',
                    ['join', 'alias', 'conditionType', 'condition', 'indexBy'],
                ],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function where(): void
    {
        $this->queryBuilder->where('predicates');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::where', ['predicates']],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function andWhere(): void
    {
        $this->queryBuilder->andWhere('predicates');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::andWhere', ['predicates']],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function orWhere(): void
    {
        $this->queryBuilder->orWhere('predicates');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::orWhere', ['predicates']],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function groupBy(): void
    {
        $this->queryBuilder->groupBy('groupBy');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::groupBy', ['groupBy']],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function addGroupBy(): void
    {
        $this->queryBuilder->addGroupBy('groupBy');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::addGroupBy', ['groupBy']],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function having(): void
    {
        $this->queryBuilder->having('having');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::having', ['having']],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function andHaving(): void
    {
        $this->queryBuilder->andHaving('having');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::andHaving', ['having']],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function orHaving(): void
    {
        $this->queryBuilder->orHaving('having');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::orHaving', ['having']],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function orderBy(): void
    {
        $this->queryBuilder->orderBy('sort', 'order');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::orderBy', ['sort', 'order']],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function addOrderBy(): void
    {
        $this->queryBuilder->addOrderBy('sort', 'order');
        self::assertFiltersEqual(
            $this->queryBuilder,
            [
                ['Acme\App\Infrastructure\Persistence\Doctrine\DqlQueryBuilder::addOrderBy', ['sort', 'order']],
            ]
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function useScalarHydration(): void
    {
        self::assertSame(
            AbstractQuery::HYDRATE_OBJECT,
            ReflectionHelper::getProtectedProperty($this->queryBuilder, 'hydrationMode')
        );

        $this->queryBuilder->useScalarHydration();

        self::assertSame(
            AbstractQuery::HYDRATE_SCALAR,
            ReflectionHelper::getProtectedProperty($this->queryBuilder, 'hydrationMode')
        );
    }

    /**
     * @throws \ReflectionException
     */
    private static function assertFiltersEqual(DqlQueryBuilder $queryBuilder, array $expectedFilters): void
    {
        self::assertSame(
            $expectedFilters,
            ReflectionHelper::getProtectedProperty($queryBuilder, 'filters')
        );
    }
}
