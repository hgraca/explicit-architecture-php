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

namespace Acme\App\Test\TestCase\Infrastructure\EventDispatcher;

use Acme\App\Core\Port\Lock\LockManagerInterface;
use Acme\App\Core\Port\Persistence\TransactionServiceInterface;
use Acme\App\Infrastructure\EventDispatcher\SyncEventDispatcher;
use Acme\App\Test\Framework\AbstractUnitTest;
use Acme\PhpExtension\Helper\ReflectionHelper;
use Exception;
use Mockery;
use Mockery\MockInterface;
use Psr\Log\LoggerInterface;

/**
 * @small
 *
 * @internal
 */
final class SyncEventDispatcherUnitTest extends AbstractUnitTest
{
    /**
     * @var MockInterface|TransactionServiceInterface
     */
    private $transactionServiceSpy;

    /**
     * @var MockInterface|LockManagerInterface
     */
    private $lockManagerMock;

    /**
     * @var MockInterface|LoggerInterface
     */
    private $loggerSpy;

    /**
     * @var SyncEventDispatcher
     */
    private $dispatcher;

    protected function setUp(): void
    {
        $this->transactionServiceSpy = Mockery::spy(TransactionServiceInterface::class);
        $this->loggerSpy = Mockery::spy(LoggerInterface::class);
        $this->lockManagerMock = Mockery::mock(LockManagerInterface::class);
        $this->dispatcher = new SyncEventDispatcher(
            $this->transactionServiceSpy,
            $this->lockManagerMock,
            $this->loggerSpy
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function dispatch(): void
    {
        $firstListenerMethod = 'doSomething';
        $secondListenerMethod = 'doSomethingElse';
        $thirdListenerMethod = 'doSomethingDifferent';

        $listener1 = Mockery::spy(DummyListener::class);
        $listener2 = Mockery::spy(DummyTwoListener::class);

        $this->dispatcher->addDestination(DummyEvent::class, [$listener1, $firstListenerMethod]);
        $this->dispatcher->addDestination(DummyEvent::class, [$listener1, $secondListenerMethod]);
        $this->dispatcher->addDestination(DummyTwoEvent::class, [$listener2, $thirdListenerMethod]);

        $event = new DummyEvent();
        $this->dispatcher->dispatch($event);

        $listener1->shouldNotHaveReceived($firstListenerMethod);
        $listener1->shouldNotHaveReceived($secondListenerMethod);
        $listener2->shouldNotHaveReceived($thirdListenerMethod);

        self::assertEquals(
            [
                [new DummyEvent(), []],
            ],
            ReflectionHelper::getProtectedProperty($this->dispatcher, 'eventBuffer')
        );
    }

    /**
     * @test
     */
    public function flush(): void
    {
        $firstListenerMethod = 'doSomething';
        $secondListenerMethod = 'doSomethingElse';
        $thirdListenerMethod = 'doSomethingDifferent';

        $listener1 = Mockery::spy(DummyListener::class);
        $listener2 = Mockery::spy(DummyTwoListener::class);

        $this->dispatcher->addDestination(DummyEvent::class, [$listener1, $firstListenerMethod]);
        $this->dispatcher->addDestination(DummyEvent::class, [$listener1, $secondListenerMethod]);
        $this->dispatcher->addDestination(DummyTwoEvent::class, [$listener2, $thirdListenerMethod]);

        $this->lockManagerMock->shouldReceive('releaseAll')->times(2);

        $event = new DummyEvent();
        $this->dispatcher->dispatch($event);
        $this->dispatcher->flush();

        $this->transactionServiceSpy->shouldHaveReceived('startTransaction');
        $listener1->shouldHaveReceived($firstListenerMethod)->with($event, []);
        $this->transactionServiceSpy->shouldHaveReceived('finishTransaction');

        $this->transactionServiceSpy->shouldHaveReceived('startTransaction');
        $listener1->shouldHaveReceived($secondListenerMethod)->with($event, []);
        $this->transactionServiceSpy->shouldHaveReceived('finishTransaction');

        $listener2->shouldNotHaveReceived($thirdListenerMethod);
        $this->loggerSpy->shouldNotHaveReceived('error');
        $this->transactionServiceSpy->shouldNotHaveReceived('rollbackTransaction');
    }

    /**
     * @test
     */
    public function flush_logs_exception_and_continues(): void
    {
        $firstListenerMethod = 'doSomething';
        $secondListenerMethod = 'doSomethingElse';
        $thirdListenerMethod = 'doSomethingDifferent';
        $errorMessage = 'Some error';

        $listener1 = function () use ($errorMessage): void {
            throw new Exception($errorMessage);
        };
        $listener2 = Mockery::spy(DummyTwoListener::class);

        $this->dispatcher->addDestination(DummyEvent::class, [$listener2, $firstListenerMethod]);
        $this->dispatcher->addDestination(DummyEvent::class, $listener1);
        $this->dispatcher->addDestination(DummyEvent::class, [$listener2, $secondListenerMethod]);
        $this->dispatcher->addDestination(DummyEvent::class, [$listener2, $thirdListenerMethod]);

        $this->lockManagerMock->shouldReceive('releaseAll')->times(4);

        $event = new DummyEvent();
        $this->dispatcher->dispatch($event);
        $this->dispatcher->flush();

        $this->transactionServiceSpy->shouldHaveReceived('startTransaction');
        $listener2->shouldHaveReceived($firstListenerMethod)->with($event, []);
        $this->transactionServiceSpy->shouldHaveReceived('finishTransaction');

        $this->transactionServiceSpy->shouldHaveReceived('startTransaction');
        $this->loggerSpy->shouldHaveReceived('error');
        $this->transactionServiceSpy->shouldHaveReceived('rollbackTransaction');

        $this->transactionServiceSpy->shouldHaveReceived('startTransaction');
        $listener2->shouldHaveReceived($secondListenerMethod)->with($event, []);
        $this->transactionServiceSpy->shouldHaveReceived('finishTransaction');

        $this->transactionServiceSpy->shouldHaveReceived('startTransaction');
        $listener2->shouldHaveReceived($secondListenerMethod)->with($event, []);
        $this->transactionServiceSpy->shouldHaveReceived('finishTransaction');
    }
}
