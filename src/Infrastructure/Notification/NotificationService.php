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

namespace Acme\App\Infrastructure\Notification;

use Acme\App\Core\Port\Notification\Exception\UndeliverableNotificationException;
use Acme\App\Core\Port\Notification\NotificationInterface;
use Acme\App\Core\Port\Notification\NotificationServiceInterface;
use Acme\App\Infrastructure\Notification\Strategy\NotificationStrategyInterface;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Nicolae Nichifor
 */
final class NotificationService implements NotificationServiceInterface
{
    /**
     * @var NotificationStrategyInterface[]
     */
    private $notificationStrategyList;

    public function __construct(NotificationStrategyInterface ...$notificationStrategyList)
    {
        foreach ($notificationStrategyList as $notificationStrategy) {
            $this->notificationStrategyList[$notificationStrategy->getType()->getValue()] = $notificationStrategy;
        }
    }

    /**
     * @throws UndeliverableNotificationException
     */
    public function notify(NotificationInterface $notification): void
    {
        foreach ($this->resolveNotificationStrategy($notification) as $notificationStrategyType) {
            $this->getNotificationStrategyForType($notificationStrategyType)->notify($notification);
        }
    }

    private function getNotificationStrategyForType(NotificationType $notificationType): NotificationStrategyInterface
    {
        return $this->notificationStrategyList[$notificationType->getValue()];
    }

    /**
     * @throws UndeliverableNotificationException
     *
     * @return NotificationType[]
     */
    private function resolveNotificationStrategy(NotificationInterface $notification): array
    {
        $deliverableBy = \array_map(
            function (NotificationStrategyInterface $strategy) {
                return $strategy->getType();
            },
            \array_filter(
                $this->notificationStrategyList,
                function (NotificationStrategyInterface $strategy) use ($notification) {
                    return $strategy->canHandleNotification($notification);
                }
            )
        );

        if (empty($deliverableBy)) {
            throw new UndeliverableNotificationException(
                'Could not find a strategy to deliver the notification ' . \get_class($notification)
            );
        }

        return $deliverableBy;
    }
}
