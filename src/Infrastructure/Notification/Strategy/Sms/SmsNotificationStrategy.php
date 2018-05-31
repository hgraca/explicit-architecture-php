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

namespace Acme\App\Infrastructure\Notification\Strategy\Sms;

use Acme\App\Core\Component\User\Application\Repository\UserRepositoryInterface;
use Acme\App\Core\Port\Notification\Client\Sms\SmsNotifierInterface;
use Acme\App\Core\Port\Notification\NotificationInterface;
use Acme\App\Infrastructure\Notification\NotificationType;
use Acme\App\Infrastructure\Notification\Settings\NotificationSettingsServiceInterface;
use Acme\App\Infrastructure\Notification\Strategy\AbstractNotificationStrategy;

/**
 * @author Coen Moij
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Nicolae Nichifor
 */
final class SmsNotificationStrategy extends AbstractNotificationStrategy
{
    /**
     * @var NotificationType
     */
    private $type;

    /**
     * @var SmsNotifierInterface
     */
    private $smsNotifier;

    /**
     * @var NotificationSettingsServiceInterface
     */
    private $smsNotificationSettingsService;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(
        SmsNotifierInterface $smsNotifier,
        NotificationSettingsServiceInterface $smsNotificationSettingsService,
        UserRepositoryInterface $userRepository
    ) {
        $this->type = NotificationType::sms();
        $this->smsNotifier = $smsNotifier;
        $this->smsNotificationSettingsService = $smsNotificationSettingsService;
        $this->userRepository = $userRepository;
    }

    public function getType(): NotificationType
    {
        return $this->type;
    }

    public function notify(NotificationInterface $notification): void
    {
        $this->smsNotifier->sendNotification($this->generateNotificationMessage($notification));
    }

    public function canHandleNotification(NotificationInterface $notification): bool
    {
        $userId = $notification->getDestinationUserId();
        $user = $this->userRepository->findOneById($userId);

        return $this->smsNotificationSettingsService->hasNotificationsEnabled($userId)
            && $user->hasMobile()
            && parent::canHandleNotification($notification);
    }
}
