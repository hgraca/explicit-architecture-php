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

namespace Acme\App\Core\Port\EventDispatcher;

use Acme\App\Core\SharedKernel\Port\EventDispatcher\EventInterface;

interface EventDispatcherInterface
{
    /**
     * The $metadata argument can be anything that should not be in the event. For example if we use some external tool
     * and we send this event to it but we also need to send extra data that is completely irrelevant to the application
     * core.
     */
    public function dispatch(EventInterface $event, array $metadata = []): void;
}
