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

use Acme\App\Core\SharedKernel\Exception\AppRuntimeException;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Nicolae Nichifor
 */
final class UnprocessableNotificationException extends AppRuntimeException
{
    public function __construct(string $notificationName, string $processorName)
    {
        parent::__construct(
            'The requested notification ' . $notificationName . ' can not be handled by ' . $processorName
        );
    }
}
