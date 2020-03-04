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

/**
 * @small
 *
 * @internal
 */
final class ResultCollectionUnitTest extends AbstractUnitTest
{
    const ITEM_LIST = [['a' => 1, 'b' => 'a'], ['a' => 2, 'b' => 'b']];

    /**
     * @var ResultCollection
     */
    private $collection;

    protected function setUp(): void
    {
        $this->collection = new ResultCollection(self::ITEM_LIST);
    }

    /**
     * @test
     */
    public function get_iterator(): void
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
    public function hydrate_as(): void
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
     */
    public function hydrate_as_throws_exception_if_not_hydratable_from_array(): void
    {
        $this->expectException(\Acme\App\Core\Port\Persistence\Exception\NotConstructableFromArrayException::class);

        $this->collection->hydrateResultItemsAs(DummyQueryC::class);
    }

    /**
     * @test
     */
    public function hydrate_as_throws_exception_if_contents_not_array(): void
    {
        $this->expectException(\Acme\App\Core\Port\Persistence\Exception\CanOnlyHydrateFromArrayException::class);

        $collection = new ResultCollection([new DummyDto(1, 'a')]);
        $collection->hydrateResultItemsAs(DummyDto::class);
    }

    /**
     * @test
     */
    public function get_single_result_throws_exception_if_more_than_one_result(): void
    {
        $this->expectException(\Acme\App\Core\Port\Persistence\Exception\NotUniqueQueryResultException::class);

        $this->collection->getSingleResult();
    }

    /**
     * @test
     */
    public function get_single_result_throws_exception_if_less_than_one_result(): void
    {
        $this->expectException(\Acme\App\Core\Port\Persistence\Exception\EmptyQueryResultException::class);

        $collection = new ResultCollection();
        $collection->getSingleResult();
    }

    /**
     * @test
     */
    public function get_single_result(): void
    {
        $a = 'A';
        $collection = new ResultCollection([$a]);
        self::assertSame($a, $collection->getSingleResult());
    }
}
