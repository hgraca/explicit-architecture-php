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

use Acme\PhpExtension\Enum\AbstractEnum;

/**
 * @author Henrique Moody
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Nicolae Nichifor
 *
 * @method static NotificationType email()
 * @method bool isEmail()
 * @method static NotificationType push()
 * @method bool isPush()
 * @method static NotificationType sms()
 * @method bool isSms()
 */
final class NotificationType extends AbstractEnum
{
    public const EMAIL = 'EMAIL';
    public const PUSH = 'PUSH';
    public const SMS = 'SMS';
}
