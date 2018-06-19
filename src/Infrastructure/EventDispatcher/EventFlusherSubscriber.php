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

namespace Acme\App\Infrastructure\EventDispatcher;

use Acme\App\Core\Port\EventDispatcher\BufferedEventDispatcherInterface;
use Acme\App\Infrastructure\Persistence\RequestTransactionSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class EventFlusherSubscriber implements EventSubscriberInterface
{
    /**
     * The default is 0.
     * The highest the priority, the earlier a listener is executed.
     * The symfony subscribers use values from -250 to +250, but we can use whatever integers we want.
     *
     * We want to execute this subscriber after committing the main use case DB transactions, so that the events
     * already have available the DB changes made by the main use case that triggered the events.
     * So we make sure the this subscriber priority is lower than the RequestTransactionSubscriber.
     */
    private const PRIORITY = RequestTransactionSubscriber::PRIORITY - 5;

    /**
     * @var BufferedEventDispatcherInterface
     */
    private $bufferedEventDispatcher;

    public function __construct(BufferedEventDispatcherInterface $bufferedEventDispatcher)
    {
        $this->bufferedEventDispatcher = $bufferedEventDispatcher;
    }

    /**
     * Return the subscribed events, their methods and possibly their priorities
     * (the higher the priority the earlier the method is called).
     *
     * @see http://symfony.com/doc/current/event_dispatcher.html#creating-an-event-subscriber
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::TERMINATE => ['flushEvents', self::PRIORITY],
            // In the case that both the Exception and Response events are triggered, we want to make sure the
            // events will not be dispatched.
            KernelEvents::EXCEPTION => ['resetEvents', self::PRIORITY + 1],
        ];
    }

    public function flushEvents(): void
    {
        $this->bufferedEventDispatcher->flush();
    }

    public function resetEvents(): void
    {
        $this->bufferedEventDispatcher->reset();
    }
}
