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

namespace Acme\App\Infrastructure\Notification\Strategy\Email;

use Acme\App\Core\Port\Notification\Client\Email\EmailerInterface;
use Acme\App\Core\Port\Notification\NotificationInterface;
use Acme\App\Infrastructure\Notification\NotificationType;
use Acme\App\Infrastructure\Notification\Strategy\AbstractNotificationStrategy;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Nicolae Nichifor
 */
final class EmailNotificationStrategy extends AbstractNotificationStrategy
{
    /**
     * @var NotificationType
     */
    private $type;

    /**
     * @var EmailerInterface
     */
    private $emailer;

    public function __construct(EmailerInterface $emailer)
    {
        $this->type = NotificationType::email();
        $this->emailer = $emailer;
    }

    public function getType(): NotificationType
    {
        return $this->type;
    }

    public function notify(NotificationInterface $notification): void
    {
        $this->emailer->send($this->generateNotificationMessage($notification));
    }
}
