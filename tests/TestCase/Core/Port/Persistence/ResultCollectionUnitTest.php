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

namespace Acme\App\Test\TestCase\Core\Port\Persistence;

use Acme\App\Core\Port\Persistence\ResultCollection;
use Acme\App\Test\Framework\AbstractUnitTest;

final class ResultCollectionUnitTest extends AbstractUnitTest
{
    const ITEM_LIST = [['a' => 1, 'b' => 'a'], ['a' => 2, 'b' => 'b']];

    /**
     * @var ResultCollection
     */
    private $collection;

    public function setUp(): void
    {
        $this->collection = new ResultCollection(self::ITEM_LIST);
    }

    /**
     * @test
     */
    public function getIterator(): void
    {
        foreach ($this->collection as $key => $item) {
            self::assertSame(self::ITEM_LIST[$key], $item);
        }
    }

    /**
     * @test
     */
    public function count_counts_number_of_items_in_collection(): void
    {
        self::assertSame(\count(self::ITEM_LIST), \count($this->collection));
    }

    /**
     * @test
     */
    public function hydrateAs(): void
    {
        $newCollection = $this->collection->hydrateResultItemsAs(DummyDto::class);

        foreach ($this->collection as $key => $item) {
            self::assertSame(self::ITEM_LIST[$key], $item);
        }

        foreach ($newCollection as $key => $item) {
            self::assertEquals(
                new DummyDto(self::ITEM_LIST[$key]['a'], self::ITEM_LIST[$key]['b']),
                $item
            );
        }
    }

    /**
     * @test
     * @expectedException \Acme\App\Core\Port\Persistence\Exception\NotConstructableFromArrayException
     */
    public function hydrateAs_throws_exception_if_not_hydratable_from_array(): void
    {
        $this->collection->hydrateResultItemsAs(DummyQueryC::class);
    }

    /**
     * @test
     * @expectedException \Acme\App\Core\Port\Persistence\Exception\CanOnlyHydrateFromArrayException
     */
    public function hydrateAs_throws_exception_if_contents_not_array(): void
    {
        $collection = new ResultCollection([new DummyDto(1, 'a')]);
        $collection->hydrateResultItemsAs(DummyDto::class);
    }

    /**
     * @test
     * @expectedException \Acme\App\Core\Port\Persistence\Exception\NotUniqueQueryResultException
     */
    public function getSingleResult_throws_exception_if_more_than_one_result(): void
    {
        $this->collection->getSingleResult();
    }

    /**
     * @test
     * @expectedException \Acme\App\Core\Port\Persistence\Exception\EmptyQueryResultException
     */
    public function getSingleResult_throws_exception_if_less_than_one_result(): void
    {
        $collection = new ResultCollection();
        $collection->getSingleResult();
    }

    /**
     * @test
     */
    public function getSingleResult(): void
    {
        $a = 'A';
        $collection = new ResultCollection([$a]);
        self::assertSame($a, $collection->getSingleResult());
    }
}
