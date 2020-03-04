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

namespace Acme\App\Infrastructure\Notification\Strategy\Push;

use Acme\App\Core\Port\Notification\Client\Push\PushNotifierInterface;
use Acme\App\Core\Port\Notification\NotificationInterface;
use Acme\App\Infrastructure\Notification\NotificationType;
use Acme\App\Infrastructure\Notification\Settings\NotificationSettingsServiceInterface;
use Acme\App\Infrastructure\Notification\Strategy\AbstractNotificationStrategy;

/**
 * @author Coen Moij
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Nicolae Nichifor
 */
final class PushNotificationStrategy extends AbstractNotificationStrategy
{
    /**
     * @var NotificationType
     */
    private $type;

    /**
     * @var PushNotifierInterface
     */
    private $pushNotifier;

    /**
     * @var NotificationSettingsServiceInterface
     */
    private $PushNotificationSettingsService;

    public function __construct(
        PushNotifierInterface $pushNotifier,
        NotificationSettingsServiceInterface $pushNotificationSettingsService
    ) {
        $this->type = NotificationType::push();
        $this->pushNotifier = $pushNotifier;
        $this->PushNotificationSettingsService = $pushNotificationSettingsService;
    }

    public function getType(): NotificationType
    {
        return $this->type;
    }

    public function notify(NotificationInterface $notification): void
    {
        $this->pushNotifier->sendNotification($this->generateNotificationMessage($notification));
    }

    public function canHandleNotification(NotificationInterface $notification): bool
    {
        $hasPushNotificationsEnabled = $this->PushNotificationSettingsService->hasNotificationsEnabled(
            $notification->getDestinationUserId()
        );

        return $hasPushNotificationsEnabled && parent::canHandleNotification($notification);
    }
}
