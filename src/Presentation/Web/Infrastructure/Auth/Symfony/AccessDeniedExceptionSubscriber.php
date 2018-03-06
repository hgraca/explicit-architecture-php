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

namespace Acme\App\Presentation\Web\Infrastructure\Auth\Symfony;

use Acme\App\Core\Port\Persistence\TransactionServiceInterface;
use Acme\App\Presentation\Web\Core\Port\Auth\AccessDeniedException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException as SymfonyAccessDeniedException;

final class AccessDeniedExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var TransactionServiceInterface
     */
    private $transactionService;

    public function __construct(TransactionServiceInterface $transactionService)
    {
        $this->transactionService = $transactionService;
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
            KernelEvents::EXCEPTION => ['handleException', 256],
        ];
    }

    public function handleException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();

        switch (true) {
            case $exception instanceof AccessDeniedException:
                /*
                 * We translate our AccessDeniedException into Symfony AccessDeniedException,
                 * so that the system continues to respond in the same way, by redirecting to the login screen
                 */
                $newException = new SymfonyAccessDeniedException($exception->getMessage(), $exception);
                $newException->setAttributes(array_merge($exception->getRoleList(), [$exception->getAction()]));
                $newException->setSubject($exception->getSubject());

                $event->setException($newException);

                break;
        }
    }
}
