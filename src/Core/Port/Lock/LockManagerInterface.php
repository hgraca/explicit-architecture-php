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

namespace Acme\App\Core\Port\Lock;

/**
 * A locking mechanism can have several properties, like being blocking or not, having a time to live, and releasing
 * automatically upon destruction or not.
 * However, in our project we will always have a blocking lock, with a maximum of 5min. ttl, and releasable upon
 * destruction. Furthermore, we never need to release only one lock, we always want to release all locks.
 * So, despite all the different options we can have in a locking mechanism, our port is quite simple because it is
 * adjusted to what we actually need in our project.
 */
interface LockManagerInterface
{
    public function acquire(string $resourceName): void;

    public function releaseAll(): void;
}
