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

use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;

/**
 * @author Coen Moij
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
interface NotificationSettingsServiceInterface
{
    public function hasNotificationsEnabled(UserId $userId): bool;

    public function enableNotifications(UserId $userId): void;

    public function disableNotifications(UserId $userId): void;
}
