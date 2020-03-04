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

namespace Acme\App\Test\TestCase\Infrastructure\Notification\Strategy\Email;

use Acme\App\Core\Port\Notification\Client\Email\Email;
use Acme\App\Core\Port\Notification\Client\Email\EmailerInterface;
use Acme\App\Infrastructure\Notification\Strategy\Email\EmailNotificationStrategy;
use Acme\App\Test\Framework\AbstractUnitTest;
use Acme\App\Test\TestCase\Infrastructure\Notification\Strategy\DummyNotification;
use Mockery;
use Mockery\MockInterface;

/**
 * @small
 *
 * @internal
 */
final class EmailNotificationStrategyUnitTest extends AbstractUnitTest
{
    private const GENERATOR_METHOD = 'generate';

    /**
     * @var MockInterface|EmailerInterface
     */
    private $mailerMock;

    /**
     * @var EmailNotificationStrategy
     */
    private $notificationStrategy;

    /**
     * @var MockInterface|DummyEmailGenerator
     */
    private $notificationMessageGeneratorMock;

    protected function setUp(): void
    {
        $this->mailerMock = Mockery::mock(EmailerInterface::class);
        $this->notificationStrategy = new EmailNotificationStrategy($this->mailerMock);

        $this->notificationMessageGeneratorMock = Mockery::mock(DummyEmailGenerator::class);

        $this->notificationStrategy->addNotificationMessageGenerator(
            $this->notificationMessageGeneratorMock,
            DummyNotification::class,
            self::GENERATOR_METHOD
        );
    }

    /**
     * @test
     */
    public function notify_Sends_out_correct_notification(): void
    {
        $generatedMessage = Mockery::mock(Email::class);

        $this->notificationMessageGeneratorMock->shouldReceive(self::GENERATOR_METHOD)
            ->once()
            ->andReturn($generatedMessage);
        $this->mailerMock->shouldReceive('send')->once()
            ->with($generatedMessage);

        $notification = new DummyNotification('hash');
        $this->notificationStrategy->notify($notification);
    }
}
