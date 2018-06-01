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

namespace Acme\App\Test\TestCase\Infrastructure\Framework\Symfony\EventSubscriber;

use Acme\App\Core\Port\Lock\LockManagerInterface;
use Acme\App\Core\Port\Persistence\TransactionServiceInterface;
use Acme\App\Infrastructure\Framework\Symfony\EventSubscriber\RequestTransactionSubscriber;
use Acme\App\Test\Framework\AbstractUnitTest;
use Mockery;
use Mockery\MockInterface;

/**
 * @small
 */
final class RequestTransactionSubscriberUnitTest extends AbstractUnitTest
{
    /**
     * @var MockInterface|TransactionServiceInterface
     */
    private $transactionServiceMock;

    /**
     * @var MockInterface|LockManagerInterface
     */
    private $lockManagerMock;

    /**
     * @var RequestTransactionSubscriber
     */
    private $subscriber;

    protected function setUp(): void
    {
        $this->transactionServiceMock = Mockery::mock(TransactionServiceInterface::class);
        $this->lockManagerMock = Mockery::mock(LockManagerInterface::class);
        $this->subscriber = new RequestTransactionSubscriber($this->transactionServiceMock, $this->lockManagerMock);
    }

    /**
     * @test
     */
    public function startTransaction(): void
    {
        $this->transactionServiceMock->shouldReceive('startTransaction')->once();
        $this->subscriber->startTransaction();
    }

    /**
     * @test
     */
    public function finishTransaction(): void
    {
        $this->transactionServiceMock->shouldReceive('finishTransaction')->once();
        $this->lockManagerMock->shouldReceive('releaseAll')->once();
        $this->subscriber->finishTransaction();
    }

    /**
     * @test
     */
    public function rollbackTransaction(): void
    {
        $this->transactionServiceMock->shouldReceive('rollbackTransaction')->once();
        $this->lockManagerMock->shouldReceive('releaseAll')->once();
        $this->subscriber->rollbackTransaction();
    }
}
