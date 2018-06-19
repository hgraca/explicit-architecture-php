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

namespace Acme\App\Infrastructure\Persistence;

use Acme\App\Core\Port\Lock\LockManagerInterface;
use Acme\App\Core\Port\Persistence\TransactionServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class RequestTransactionSubscriber implements EventSubscriberInterface
{
    /**
     * The default is 0.
     * The highest the priority, the earlier a listener is executed.
     * The symfony subscribers use values from -250 to +250, but we can use whatever integers we want.
     */
    public const PRIORITY = 10;

    /**
     * @var TransactionServiceInterface
     */
    private $transactionService;

    /**
     * @var LockManagerInterface
     */
    private $lockManager;

    public function __construct(
        TransactionServiceInterface $transactionService,
        LockManagerInterface $lockManager
    ) {
        $this->transactionService = $transactionService;
        $this->lockManager = $lockManager;
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
            KernelEvents::CONTROLLER => ['startTransaction', self::PRIORITY],
            KernelEvents::RESPONSE => ['finishTransaction', self::PRIORITY],
            // In the case that both the Exception and Response events are triggered, we want to make sure the
            // transaction is rolled back before trying to commit it.
            KernelEvents::EXCEPTION => ['rollbackTransaction', self::PRIORITY + 1],
        ];
    }

    public function startTransaction(): void
    {
        $this->transactionService->startTransaction();
    }

    public function finishTransaction(): void
    {
        // This is is when the ORM writes all staged changes to the DB so we should do this only once in a request,
        // and only if the use case was successful.
        // If we would use a command bus, we would do this in one of its middlewares.
        $this->transactionService->finishTransaction();

        // We release all locks here, so that they can be reacquired when processing the events in the
        // EventFlusherSubscriber, which runs after this subscriber.
        $this->lockManager->releaseAll();
    }

    public function rollbackTransaction(): void
    {
        $this->transactionService->rollbackTransaction();
        $this->lockManager->releaseAll();
    }
}
