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

namespace Acme\App\Infrastructure\Lock\Symfony;

use Acme\App\Core\Port\Lock\LockAlreadyExistsException;
use Acme\App\Core\Port\Lock\LockManagerInterface;
use Acme\App\Core\Port\Lock\LockNotFoundException;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Lock;
use Symfony\Component\Lock\Store\FlockStore;

final class LockManager implements LockManagerInterface
{
    /**
     * @var Lock[]
     */
    private $lockList = [];

    /**
     * @var Factory
     */
    private $factory;

    public function __construct()
    {
        $this->factory = new Factory(new FlockStore(sys_get_temp_dir()));
    }

    public function acquire(string $resourceName): void
    {
        $lock = $this->hasLock($resourceName)
            ? $this->getLock($resourceName)
            : $this->createLock($resourceName);
        $lock->acquire(true);
    }

    public function releaseAll(): void
    {
        foreach ($this->lockList as $resourceName => $lock) {
            $lock->release();
        }
    }

    private function createLock(string $resourceName): Lock
    {
        if (!$this->hasLock($resourceName)) {
            $this->storeLock($resourceName, $this->factory->createLock($resourceName));
        }

        return $this->getLock($resourceName);
    }

    private function storeLock(string $resourceName, Lock $lock): void
    {
        if ($this->hasLock($resourceName)) {
            throw new LockAlreadyExistsException();
        }
        $this->lockList[$resourceName] = $lock;
    }

    private function getLock(string $resourceName): Lock
    {
        if (!$this->hasLock($resourceName)) {
            throw new LockNotFoundException();
        }

        return $this->lockList[$resourceName];
    }

    private function hasLock(string $resourceName): bool
    {
        return isset($this->lockList[$resourceName]);
    }
}
