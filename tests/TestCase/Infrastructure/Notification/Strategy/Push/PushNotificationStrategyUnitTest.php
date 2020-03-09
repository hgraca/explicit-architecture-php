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

namespace Acme\App\Test\TestCase\Infrastructure\Notification\Strategy\Push;

use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Core\Port\Notification\Client\Push\PushNotification;
use Acme\App\Core\Port\Notification\Client\Push\PushNotifierInterface;
use Acme\App\Core\Port\Notification\Client\Push\PushNotifierResponse;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;
use Acme\App\Infrastructure\Notification\Settings\NotificationSettingsServiceInterface;
use Acme\App\Infrastructure\Notification\Strategy\Push\PushNotificationStrategy;
use Acme\App\Test\Framework\AbstractUnitTest;
use Acme\App\Test\TestCase\Infrastructure\Notification\Strategy\DummyNotification;
use Mockery;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @small
 *
 * @internal
 */
final class PushNotificationStrategyUnitTest extends AbstractUnitTest
{
    private const GENERATOR_METHOD = 'generate';

    /**
     * @var MockInterface|PushNotifierInterface
     */
    private $pushNotifierMock;

    /**
     * @var PushNotificationStrategy
     */
    private $notificationStrategy;

    /**
     * @var MockInterface|DummyPushGenerator
     */
    private $notificationMessageGeneratorMock;

    /**
     * @var NotificationSettingsServiceInterface|MockInterface
     */
    private $settingsServiceMock;

    protected function setUp(): void
    {
        $this->pushNotifierMock = Mockery::mock(PushNotifierInterface::class);
        $this->settingsServiceMock = Mockery::mock(NotificationSettingsServiceInterface::class);
        $this->notificationStrategy = new PushNotificationStrategy($this->pushNotifierMock, $this->settingsServiceMock);

        $this->notificationMessageGeneratorMock = Mockery::mock(DummyPushGenerator::class);

        $this->notificationStrategy->addNotificationMessageGenerator(
            $this->notificationMessageGeneratorMock,
            DummyNotification::class,
            self::GENERATOR_METHOD
        );
    }

    /**
     * @test
     */
    public function notify_sends_out_correct_notification(): void
    {
        $generatedMessage = new PushNotification('short name', 'title', 'message', new UserId(), []);

        $bodyMock = Mockery::mock(StreamInterface::class);
        $bodyMock->shouldReceive('getContents')->once()->andReturn('contents');

        $responseMock = Mockery::mock(ResponseInterface::class);
        $responseMock->shouldReceive('getStatusCode')->once()->andReturn(200);
        $responseMock->shouldReceive('getBody')->once()->andReturn($bodyMock);

        $this->notificationMessageGeneratorMock->shouldReceive(self::GENERATOR_METHOD)
            ->once()
            ->andReturn($generatedMessage);
        $this->pushNotifierMock->shouldReceive('sendNotification')->once()
            ->with($generatedMessage)
            ->andReturn(new PushNotifierResponse($responseMock));

        $this->settingsServiceMock->shouldReceive('hasNotificationsEnabled')->once()->andReturnTrue();

        $notification = new DummyNotification('hash', $this->createUser()->getId());
        $this->notificationStrategy->notify($notification);
    }

    /**
     * @test
     * @dataProvider canHandleNotificationDataProvider
     */
    public function can_handle_notification_returns_true_for_users_with_push_notifications_enabled(
        bool $hasPushNotificationsEnabled,
        bool $expected
    ): void {
        $this->settingsServiceMock->shouldReceive('hasNotificationsEnabled')->once()->andReturn(
            $hasPushNotificationsEnabled
        );

        $notification = new DummyNotification('hash', $this->createUser()->getId());

        self::assertEquals($expected, $this->notificationStrategy->canHandleNotification($notification));
    }

    public function canHandleNotificationDataProvider(): array
    {
        return [
            [true, true],
            [false, false],
        ];
    }

    private function createUser(): User
    {
        return $user = User::constructWithoutPassword(
            'username',
            'email@foo.com',
            '+31631769214',
            'full name',
            User::ROLE_EDITOR
        );
    }
}
