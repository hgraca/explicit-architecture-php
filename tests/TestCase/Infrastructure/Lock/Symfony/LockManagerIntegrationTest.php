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

namespace Acme\App\Test\TestCase\Infrastructure\Lock\Symfony;

use Acme\App\Core\Port\Lock\LockManagerInterface;
use Acme\App\Test\Framework\AbstractIntegrationTest;
use Acme\PhpExtension\Helper\ReflectionHelper;
use Symfony\Component\Lock\Lock;

/**
 * @medium
 */
final class LockManagerIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function acquire_creates_new_lock_and_acquires_it(): void
    {
        $lockName = 'lock1';
        $lockManager = $this->getLockManager();
        $lockManager->acquire($lockName);

        /** @var Lock[] $lockList */
        $lockList = ReflectionHelper::getProtectedProperty($lockManager, 'lockList');

        self::assertTrue($lockList[$lockName]->isAcquired());
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function acquire_reuses_existing_lock(): void
    {
        $lockName = 'lock1';
        $lockManager = $this->getLockManager();
        $lockManager->acquire($lockName);

        /** @var Lock[] $lockList */
        $lockList = ReflectionHelper::getProtectedProperty($lockManager, 'lockList');
        $lockBeforeReacquiring = $lockList[$lockName];

        $lockManager->releaseAll();
        $lockManager->acquire($lockName);

        /** @var Lock[] $lockList */
        $lockList = ReflectionHelper::getProtectedProperty($lockManager, 'lockList');
        $lockAfterReacquiring = $lockList[$lockName];

        self::assertSame($lockBeforeReacquiring, $lockAfterReacquiring);
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function releaseAll(): void
    {
        $lockManager = $this->getLockManager();
        $lockManager->acquire('lock1');
        $lockManager->acquire('lock2');
        $lockManager->acquire('lock3');
        $lockManager->releaseAll();

        /** @var Lock[] $lockList */
        $lockList = ReflectionHelper::getProtectedProperty($lockManager, 'lockList');

        foreach ($lockList as $lock) {
            self::assertFalse($lock->isAcquired());
        }
    }

    private function getLockManager(): LockManagerInterface
    {
        return self::getService(LockManagerInterface::class);
    }
}
