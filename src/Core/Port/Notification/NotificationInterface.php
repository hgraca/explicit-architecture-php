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

namespace Acme\App\Core\Port\Notification;

use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;

/**
 * This interface has no functional value, because it does not require a set of methods, but it does have semantic
 * value: It will explicitly tell us that an object implementing this interface is a notification
 * We will also know that any object not implementing this interface is not designed with the intention of being
 * dispatched by the notification dispatcher.
 *
 * Furthermore, it allows us to strong type the argument of the notification dispatcher, preventing us from making
 * the mistake of dispatching objects that are not notifications.
 *
 * We don't need to specify any other methods because there will be a specific generator for each specific notification,
 * so the generator will know the specific notification class and therefore will know what other methods are available.
 */
interface NotificationInterface
{
    public function getDestinationUserId(): UserId;
}
