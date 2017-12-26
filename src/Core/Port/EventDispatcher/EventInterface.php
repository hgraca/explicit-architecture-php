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

/**
 * This interface has no functional value, because it does not require a set of methods, but it does have semantic
 * value: It will explicitly tell us that an object implementing this interface is an event, and it was designed with
 * the intention of being used with the event dispatcher. We will also know that any object not implementing this
 * interface is not designed with the intention of being dispatched by the event dispatcher.
 *
 * Furthermore, it allows us to strong type the argument of the event dispatcher, preventing us from making the mistake
 * of dispatching objects that are not intended to be dispatched by the event dispatcher.
 */
interface EventInterface
{
}
