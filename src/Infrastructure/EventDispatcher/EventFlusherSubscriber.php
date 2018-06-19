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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class EventFlusherSubscriber implements EventSubscriberInterface
{
    private const DEFAULT_PRIORITY = 5;

    /**
     * @var BufferedEventDispatcherInterface
     */
    private $bufferedEventDispatcher;

    /**
     * @var int
     */
    private static $priority = self::DEFAULT_PRIORITY;

    public function __construct(
        BufferedEventDispatcherInterface $bufferedEventDispatcher,
        int $eventFlusherSubscriberPriority = self::DEFAULT_PRIORITY
    ) {
        $this->bufferedEventDispatcher = $bufferedEventDispatcher;
        self::$priority = $eventFlusherSubscriberPriority;
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
            KernelEvents::TERMINATE => ['flushEvents', self::$priority],
            // In the case that both the Exception and Response events are triggered, we want to make sure the
            // events will not be dispatched.
            KernelEvents::EXCEPTION => ['resetEvents', self::$priority + 1],
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
