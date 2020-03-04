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

use Acme\App\Infrastructure\Persistence\Doctrine\DqlPersistenceService;
use Acme\App\Test\Framework\AbstractUnitTest;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Mockery\MockInterface;

/**
 * @small
 *
 * @internal
 */
final class DQLPersistenceServiceUnitTest extends AbstractUnitTest
{
    const PAGE = 2;
    const ITEMS_PER_PAGE = 3;

    /**
     * @var MockInterface|EntityManagerInterface
     */
    private $entityManagerMock;

    /**
     * @var Connection
     */
    private $connectionMock;

    protected function setUp(): void
    {
        $this->connectionMock = self::mock(Connection::class);
        $this->entityManagerMock = self::mock(EntityManagerInterface::class);
        $this->entityManagerMock->shouldReceive('getConnection')->andReturn($this->connectionMock);
    }

    /**
     * @test
     */
    public function upsert(): void
    {
        $entity = new DummyEntity();
        $this->entityManagerMock->shouldReceive('persist')->once()->with($entity);
        $this->getPersistenceServiceWithAutoCommit()->upsert($entity);
    }

    /**
     * @test
     */
    public function delete(): void
    {
        $entity = new DummyEntity();
        $this->entityManagerMock->shouldReceive('remove')->once()->with($entity);
        $this->getPersistenceServiceWithAutoCommit()->delete($entity);
    }

    /**
     * @test
     */
    public function start_transaction_with_auto_commit_does_not_start_transaction(): void
    {
        $this->connectionMock->shouldNotReceive('beginTransaction');

        $this->getPersistenceServiceWithAutoCommit()->startTransaction();
    }

    /**
     * @test
     */
    public function start_transaction_without_auto_commit_starts_transaction(): void
    {
        $this->connectionMock->shouldReceive('beginTransaction')->once();

        $this->getPersistenceServiceWithoutAutoCommit()->startTransaction();
    }

    /**
     * @test
     *
     * @throws ConnectionException
     */
    public function finish_transaction_with_auto_commit_does_not_commit_transaction(): void
    {
        $this->connectionMock->shouldNotReceive('isTransactionActive');
        $this->connectionMock->shouldNotReceive('commit');

        $this->entityManagerMock->shouldReceive('flush');

        $this->getPersistenceServiceWithAutoCommit()->finishTransaction();
    }

    /**
     * @test
     *
     * @throws ConnectionException
     */
    public function finish_transaction_without_auto_commit_commits_transaction(): void
    {
        $this->connectionMock->shouldReceive('isTransactionActive')->once()->andReturn(true);
        $this->connectionMock->shouldReceive('commit')->once();

        $this->entityManagerMock->shouldReceive('flush');

        $this->getPersistenceServiceWithoutAutoCommit()->finishTransaction();
    }

    /**
     * @test
     *
     * @throws ConnectionException
     */
    public function rollback_transaction_with_auto_commit_should_not_roll_back(): void
    {
        $this->connectionMock->shouldNotReceive('isTransactionActive');
        $this->connectionMock->shouldNotReceive('rollback');

        $this->entityManagerMock->shouldReceive('clear')->once();

        $this->getPersistenceServiceWithAutoCommit()->rollbackTransaction();
    }

    /**
     * @test
     *
     * @throws ConnectionException
     */
    public function rollback_transaction_without_auto_commit_should_roll_back_when_a_transaction_is_active(): void
    {
        $this->connectionMock->shouldReceive('isTransactionActive')->once()->andReturn(true);
        $this->connectionMock->shouldReceive('rollBack')->once();

        $this->entityManagerMock->shouldReceive('clear')->once();

        $this->getPersistenceServiceWithoutAutoCommit()->rollbackTransaction();
    }

    /**
     * @test
     *
     * @throws ConnectionException
     */
    public function rollback_transaction_without_auto_commit_should_not_roll_back_when_no_transaction_is_active(): void
    {
        $this->connectionMock->shouldReceive('isTransactionActive')->once()->andReturn(false);
        $this->connectionMock->shouldNotReceive('rollback');

        $this->entityManagerMock->shouldReceive('clear')->once();

        $this->getPersistenceServiceWithoutAutoCommit()->rollbackTransaction();
    }

    private function getPersistenceServiceWithoutAutoCommit(): DqlPersistenceService
    {
        return new DqlPersistenceService($this->entityManagerMock, false);
    }

    private function getPersistenceServiceWithAutoCommit(): DqlPersistenceService
    {
        return new DqlPersistenceService($this->entityManagerMock);
    }
}
