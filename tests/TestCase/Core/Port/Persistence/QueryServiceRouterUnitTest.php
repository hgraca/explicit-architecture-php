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

use Acme\App\Core\Port\Persistence\QueryServiceRouter;
use Acme\App\Test\Framework\AbstractUnitTest;
use Acme\PhpExtension\Helper\ReflectionHelper;

/**
 * @small
 *
 * @internal
 */
final class QueryServiceRouterUnitTest extends AbstractUnitTest
{
    /**
     * @var DummyQueryService
     */
    private $queryServiceA;

    /**
     * @var DummyQueryService
     */
    private $queryServiceB;

    public function initialize(): void
    {
        $this->queryServiceA = new DummyQueryService(DummyQueryA::class);
        $this->queryServiceB = new DummyQueryService(DummyQueryB::class);
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function construct(): void
    {
        $this->initialize();
        $queryServiceRouter = new QueryServiceRouter($this->queryServiceA, $this->queryServiceB);

        self::assertSame(
            [
                $this->queryServiceA->canHandle() => $this->queryServiceA,
                $this->queryServiceB->canHandle() => $this->queryServiceB,
            ],
            ReflectionHelper::getProtectedProperty($queryServiceRouter, 'queryServiceList')
        );
    }

    /**
     * @test
     */
    public function construct_throws_exception_if_service_not_callable(): void
    {
        $this->expectException(\Acme\App\Core\Port\Persistence\Exception\QueryServiceIsNotCallableException::class);

        new QueryServiceRouter(new DummyNonCallableQueryService());
    }

    /**
     * @test
     */
    public function query(): void
    {
        $this->initialize();
        $queryServiceRouter = new QueryServiceRouter($this->queryServiceA, $this->queryServiceB);
        $queryServiceRouter->query(new DummyQueryA());

        self::assertSame(DummyQueryA::class, $this->queryServiceA->getInvokeWasCalledWith());
        self::assertNull($this->queryServiceB->getInvokeWasCalledWith());

        $queryServiceRouter = new QueryServiceRouter($this->queryServiceA, $this->queryServiceB);
        $queryServiceRouter->query(new DummyQueryB());

        self::assertSame(DummyQueryA::class, $this->queryServiceA->getInvokeWasCalledWith());
        self::assertSame(DummyQueryB::class, $this->queryServiceB->getInvokeWasCalledWith());
    }

    /**
     * @test
     */
    public function query_throws_exception_if_can_not_handle_query(): void
    {
        $this->expectException(\Acme\App\Core\Port\Persistence\Exception\UnableToHandleQueryException::class);

        $this->initialize();
        $queryServiceRouter = new QueryServiceRouter($this->queryServiceA, $this->queryServiceB);
        $queryServiceRouter->query(new DummyQueryC());
    }
}
