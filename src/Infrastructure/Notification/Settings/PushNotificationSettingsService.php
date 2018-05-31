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

namespace Acme\App\Infrastructure\Notification\Settings;

use Acme\App\Core\Port\Persistence\UserKeyValueStorageInterface;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;

/**
 * @author Coen Moij
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
final class PushNotificationSettingsService implements NotificationSettingsServiceInterface
{
    private const NAMESPACE = 'push';
    private const KEY = 'enabled';
    private const ENABLED = '1';
    private const DISABLED = '0';

    /**
     * @var \Acme\App\Core\Port\Persistence\UserKeyValueStorageInterface
     */
    private $storage;

    public function __construct(UserKeyValueStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function hasNotificationsEnabled(UserId $userId): bool
    {
        return $this->storage->get($userId, self::NAMESPACE, self::KEY) === self::ENABLED;
    }

    public function enableNotifications(UserId $userId): void
    {
        $this->storage->set($userId, self::NAMESPACE, self::KEY, self::ENABLED);
    }

    public function disableNotifications(UserId $userId): void
    {
        $this->storage->set($userId, self::NAMESPACE, self::KEY, self::DISABLED);
    }
}
