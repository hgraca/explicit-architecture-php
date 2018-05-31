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

namespace Acme\App\Test\TestCase\Infrastructure\Notification;

use Acme\App\Core\Port\Notification\NotificationInterface;
use Acme\App\Infrastructure\Notification\NotificationService;
use Acme\App\Infrastructure\Notification\NotificationType;
use Acme\App\Infrastructure\Notification\Strategy\NotificationStrategyInterface;
use Acme\App\Test\Framework\AbstractUnitTest;
use Mockery;
use Mockery\MockInterface;

/**
 * @small
 */
final class NotificationServiceUnitTest extends AbstractUnitTest
{
    /**
     * @test
     */
    public function onlyExpectedStrategiesGetToNotify(): void
    {
        /* @var NotificationInterface|MockInterface $notification */
        $notification = Mockery::mock(NotificationInterface::class);

        $notificationType = NotificationType::email();
        $emailStrategyMock = $this->createStrategyMockThatShouldNotify($notificationType, $notification);
        $pushStrategyMock = $this->createStrategyMockThatShouldNotNotify(NotificationType::push());
        $smsStrategyMock = $this->createStrategyMockThatShouldNotNotify(NotificationType::sms());

        $notificationService = new NotificationService($emailStrategyMock, $pushStrategyMock, $smsStrategyMock);

        $notificationService->notify($notification);
    }

    private function createStrategyMockThatShouldNotify(
        NotificationType $type,
        $notification
    ): NotificationStrategyInterface {
        /* @var NotificationStrategyInterface|MockInterface $strategyMock */
        $strategyMock = Mockery::mock(NotificationStrategyInterface::class);
        $strategyMock->shouldReceive('getType')->twice()->andReturn($type);
        $strategyMock->shouldReceive('notify')->once()->with($notification);
        $strategyMock->shouldReceive('canHandleNotification')->once()->with($notification)->andReturn(true);

        return $strategyMock;
    }

    /**
     * @return MockInterface|NotificationStrategyInterface
     */
    private function createStrategyMockThatShouldNotNotify(NotificationType $type): NotificationStrategyInterface
    {
        $strategyMock = Mockery::mock(NotificationStrategyInterface::class);
        $strategyMock->shouldReceive('canHandleNotification')->once()->andReturnFalse();
        $strategyMock->shouldReceive('getType')->once()->andReturn($type);
        $strategyMock->shouldNotReceive('notify');

        return $strategyMock;
    }
}
