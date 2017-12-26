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
 * The events will not be immediately dispatched, they will be buffered and flushed at the end of the HTTP request.
 */
interface BufferedEventDispatcherInterface extends EventDispatcherInterface
{
    public function flush(): void;

    public function reset(): void;
}
