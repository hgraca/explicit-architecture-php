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

namespace Acme\App\Test\TestCase\Infrastructure\Notification\Strategy\Sms;

use Acme\App\Core\Component\User\Application\Repository\UserRepositoryInterface;
use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Core\Port\Notification\Client\Sms\Sms;
use Acme\App\Core\Port\Notification\Client\Sms\SmsNotifierInterface;
use Acme\App\Infrastructure\Notification\Settings\NotificationSettingsServiceInterface;
use Acme\App\Infrastructure\Notification\Strategy\Sms\SmsNotificationStrategy;
use Acme\App\Test\Framework\AbstractUnitTest;
use Acme\App\Test\TestCase\Infrastructure\Notification\Strategy\DummyNotification;
use Mockery;
use Mockery\MockInterface;

/**
 * @small
 *
 * @internal
 */
final class SmsNotificationStrategyUnitTest extends AbstractUnitTest
{
    private const TO_PHONE_NUMBER = '+31631769217';
    private const SMS_CONTENT = 'some content ...';
    private const GENERATOR_METHOD = 'generate';

    /**
     * @var MockInterface|SmsNotifierInterface
     */
    private $smsNotifierMock;

    /**
     * @var SmsNotificationStrategy
     */
    private $notificationStrategy;

    /**
     * @var MockInterface|DummySmsGenerator
     */
    private $notificationMessageGeneratorMock;

    /**
     * @var NotificationSettingsServiceInterface|MockInterface
     */
    private $settingsServiceMock;

    /**
     * @var MockInterface|UserRepositoryInterface
     */
    private $userRepositoryMock;

    protected function setUp(): void
    {
        $this->smsNotifierMock = Mockery::mock(SmsNotifierInterface::class);
        $this->settingsServiceMock = Mockery::mock(NotificationSettingsServiceInterface::class);
        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);
        $this->notificationStrategy = new SmsNotificationStrategy(
            $this->smsNotifierMock,
            $this->settingsServiceMock,
            $this->userRepositoryMock
        );

        $this->notificationMessageGeneratorMock = Mockery::mock(DummySmsGenerator::class);

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
        $generatedMessage = new Sms(self::SMS_CONTENT, self::TO_PHONE_NUMBER);

        $this->notificationMessageGeneratorMock->shouldReceive(self::GENERATOR_METHOD)
            ->once()
            ->andReturn($generatedMessage);
        $this->smsNotifierMock->shouldReceive('sendNotification')->once()
            ->with($generatedMessage);

        $this->settingsServiceMock->shouldReceive('hasNotificationsEnabled')->once()->andReturnTrue();

        $user = $this->createUser();
        $this->userRepositoryMock->shouldReceive('findOneById')->once()->with($user->getId())->andReturn($user);
        $notification = new DummyNotification('hash', $user->getId());
        $this->notificationStrategy->notify($notification);
    }

    /**
     * @test
     * @dataProvider canHandleNotificationDataProvider
     */
    public function users_with_phone_number_and_sms_notifications_enabled_can_handle_sms_notification(
        bool $smsNotificationsEnabled,
        string $phoneNumber,
        bool $expected
    ): void {
        $this->settingsServiceMock->shouldReceive('hasNotificationsEnabled')->andReturn($smsNotificationsEnabled);

        $user = User::constructWithoutPassword('a', 'b', $phoneNumber, 'd', 'e');
        $this->userRepositoryMock->shouldReceive('findOneById')->once()->with($user->getId())->andReturn($user);
        $notification = new DummyNotification('hash', $user->getId());

        self::assertEquals($expected, $this->notificationStrategy->canHandleNotification($notification));
    }

    public function canHandleNotificationDataProvider(): array
    {
        return [
            [true, '', false],
            [true, self::TO_PHONE_NUMBER, true],
            [false, '', false],
            [false, self::TO_PHONE_NUMBER, false],
        ];
    }

    private function createUser(): User
    {
        return $user = User::constructWithoutPassword(
            'username',
            'email@foo.com',
            self::TO_PHONE_NUMBER,
            'full name',
            User::ROLE_USER
        );
    }
}
