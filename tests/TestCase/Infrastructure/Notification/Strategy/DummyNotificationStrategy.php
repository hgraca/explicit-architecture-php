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

namespace Acme\App\Test\TestCase\Infrastructure\Notification\Strategy;

use Acme\App\Core\Port\Notification\NotificationInterface;
use Acme\App\Infrastructure\Notification\NotificationType;
use Acme\App\Infrastructure\Notification\Strategy\AbstractNotificationStrategy;

final class DummyNotificationStrategy extends AbstractNotificationStrategy
{
    /**
     * @var NotificationType
     */
    private $type;

    public function __construct()
    {
        $this->type = NotificationType::email();
    }

    public function getType(): NotificationType
    {
        return $this->type;
    }

    public function notify(NotificationInterface $notification): void
    {
        // not needed for the tests
    }
}
