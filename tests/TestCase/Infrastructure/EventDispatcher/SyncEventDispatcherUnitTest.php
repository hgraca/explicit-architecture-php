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

use Acme\App\Infrastructure\EventDispatcher\SyncEventDispatcher;
use Acme\App\Test\Framework\AbstractUnitTest;
use Acme\PhpExtension\Helper\ReflectionHelper;
use Mockery;

final class SyncEventDispatcherUnitTest extends AbstractUnitTest
{
    /**
     * @var SyncEventDispatcher
     */
    private $dispatcher;

    public function setUp(): void
    {
        $this->dispatcher = new SyncEventDispatcher();
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

        $event = new DummyEvent();
        $this->dispatcher->dispatch($event);
        $this->dispatcher->flush();

        $listener1->shouldHaveReceived($firstListenerMethod)->with($event, []);
        $listener1->shouldHaveReceived($secondListenerMethod)->with($event, []);
        $listener2->shouldNotHaveReceived($thirdListenerMethod);
    }
}
