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

namespace Acme\App\Test\TestCase\Infrastructure\Notification\Settings;

use Acme\App\Core\Port\Persistence\UserKeyValueStorageInterface;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;
use Acme\App\Infrastructure\Notification\Settings\PushNotificationSettingsService;
use Acme\App\Test\Framework\AbstractUnitTest;
use Mockery;
use Mockery\MockInterface;

/**
 * @small
 */
final class PushNotificationSettingsServiceUnitTest extends AbstractUnitTest
{
    private const NAMESPACE = 'push';
    private const KEY = 'enabled';
    private const ENABLED = '1';
    private const DISABLED = '0';

    /**
     * @var UserKeyValueStorageInterface|MockInterface
     */
    private $storageMock;

    /**
     * @var PushNotificationSettingsService
     */
    private $settingsService;

    protected function setUp(): void
    {
        $this->storageMock = Mockery::mock(UserKeyValueStorageInterface::class);
        $this->settingsService = new PushNotificationSettingsService($this->storageMock);
    }

    /**
     * @test
     */
    public function hasNotificationsEnabled_true(): void
    {
        $userId = new UserId();

        $this->storageMock->shouldReceive('get')->withArgs([$userId, self::NAMESPACE, self::KEY])->andReturn(true);

        self::assertTrue($this->settingsService->hasNotificationsEnabled($userId));
    }

    /**
     * @test
     */
    public function hasNotificationsEnabled_false(): void
    {
        $userId = new UserId();

        $this->storageMock->shouldReceive('get')->withArgs([$userId, self::NAMESPACE, self::KEY])->andReturn(false);

        self::assertFalse($this->settingsService->hasNotificationsEnabled($userId));
    }

    /**
     * @test
     */
    public function enableNotifications(): void
    {
        $userId = new UserId();

        $this->storageMock->shouldReceive('set')->withArgs([$userId, self::NAMESPACE, self::KEY, self::ENABLED]);

        $this->settingsService->enableNotifications($userId);
    }

    /**
     * @test
     */
    public function disableNotifications(): void
    {
        $userId = new UserId();

        $this->storageMock->shouldReceive('set')->withArgs([$userId, self::NAMESPACE, self::KEY, self::DISABLED]);

        $this->settingsService->disableNotifications($userId);
    }
}
