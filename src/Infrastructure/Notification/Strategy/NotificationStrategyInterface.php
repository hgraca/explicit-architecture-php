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

namespace Acme\App\Infrastructure\Notification\Strategy;

use Acme\App\Core\Port\Notification\NotificationInterface;
use Acme\App\Infrastructure\Notification\NotificationType;

interface NotificationStrategyInterface
{
    public function getType(): NotificationType;

    public function notify(NotificationInterface $notification): void;

    public function canHandleNotification(NotificationInterface $notification): bool;
}
