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

use Acme\App\Infrastructure\Persistence\Doctrine\DqlQuery;
use Acme\App\Test\Framework\AbstractUnitTest;

/**
 * @small
 *
 * @internal
 */
final class DQLQueryUnitTest extends AbstractUnitTest
{
    /**
     * @var array
     */
    private $filters;

    protected function setUp(): void
    {
        $this->filters = ['a', 'b', 'c'];
    }

    /**
     * @test
     *
     * @return mixed
     */
    public function get_filters(): void
    {
        $query = new DqlQuery($this->filters);

        self::assertSame($this->filters, $query->getFilters());
    }

    /**
     * @test
     */
    public function set_hydration_mode_and_get_hydration_mode(): void
    {
        $hydrationMode = 999;
        $query = new DqlQuery($this->filters);
        $query->setHydrationMode($hydrationMode);

        self::assertSame($hydrationMode, $query->getHydrationMode());
    }
}
