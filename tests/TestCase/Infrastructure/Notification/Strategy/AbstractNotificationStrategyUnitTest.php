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

namespace Acme\App\Test\TestCase\Infrastructure\Notification\Strategy;

use Acme\App\Infrastructure\Notification\Strategy\AbstractNotificationStrategy;
use Acme\App\Infrastructure\Notification\StrategyDefinition;
use Acme\App\Test\Framework\AbstractUnitTest;
use Acme\PhpExtension\Helper\ReflectionHelper;
use Mockery;
use Mockery\MockInterface;

final class AbstractNotificationStrategyUnitTest extends AbstractUnitTest
{
    private const GENERATOR_METHOD_NAME = 'generate';
    private const GENERATOR_INVALID_METHOD_NAME = 'random';
    private const VOTER_METHOD_NAME = 'vote';
    private const VOTER_INVALID_METHOD_NAME = 'random';

    /**
     * @var AbstractNotificationStrategy
     */
    private $notificationStrategy;

    /**
     * @var DummyGenerator|MockInterface
     */
    private $generator;

    /**
     * @var DummyVoter|MockInterface
     */
    private $voter;

    protected function setUp(): void
    {
        $this->notificationStrategy = new DummyNotificationStrategy();
        $this->generator = Mockery::mock(DummyGenerator::class);
        $this->voter = Mockery::mock(DummyVoter::class);
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function addNotificationMessageGenerator_CreatesTheCorrectStructure(): void
    {
        $notificationName = 'some_name';

        $this->notificationStrategy->addNotificationMessageGenerator(
            $this->generator,
            $notificationName,
            self::GENERATOR_METHOD_NAME
        );

        $notificationGeneratorList = ReflectionHelper::getProtectedProperty(
            $this->notificationStrategy,
            'notificationGeneratorList'
        );

        self::assertEquals(
            [
                $notificationName => new StrategyDefinition($this->generator, self::GENERATOR_METHOD_NAME),
            ],
            $notificationGeneratorList
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function generateNotificationMessage_GetsTheCorrectMessage(): void
    {
        $notification = new DummyNotification('hash');

        $this->notificationStrategy->addNotificationMessageGenerator(
            $this->generator,
            \get_class($notification),
            self::GENERATOR_METHOD_NAME
        );

        $this->generator->shouldReceive(self::GENERATOR_METHOD_NAME)
            ->with($notification);

        ReflectionHelper::invokeProtectedMethod(
            $this->notificationStrategy,
            'generateNotificationMessage',
            [$notification]
        );
    }

    /**
     * @test
     * @expectedException \Acme\App\Infrastructure\Notification\Strategy\InvalidGeneratorException
     */
    public function addNotificationMessageGenerator_throwsExceptionWhenGeneratorMethodDoesNotExist(): void
    {
        $this->notificationStrategy->addNotificationMessageGenerator(
            $this->generator,
            DummyNotification::class,
            self::GENERATOR_INVALID_METHOD_NAME
        );
    }

    /**
     * @test
     * @expectedException \Acme\App\Infrastructure\Notification\Strategy\InvalidVoterException
     *
     * @throws \ReflectionException
     */
    public function isAllowedForNotification_throwsExceptionWhenVoterMethodDoesNotExist(): void
    {
        $notification = new DummyNotification('hash');

        $this->notificationStrategy->addNotificationMessageGenerator(
            $this->generator,
            \get_class($notification),
            self::GENERATOR_METHOD_NAME,
            $this->voter,
            self::VOTER_INVALID_METHOD_NAME
        );

        ReflectionHelper::invokeProtectedMethod(
            $this->notificationStrategy,
            'isAllowedForNotification',
            [$notification]
        );
    }

    /**
     * @test
     * @expectedException \Acme\App\Infrastructure\Notification\Strategy\UnprocessableNotificationException
     *
     * @throws \ReflectionException
     */
    public function generateNotificationMessage_ThrowsExceptionWhenNotificationNotFound(): void
    {
        $notification = new DummyNotification('hash');

        ReflectionHelper::invokeProtectedMethod(
            $this->notificationStrategy,
            'generateNotificationMessage',
            [$notification]
        );
    }

    /**
     * @test
     */
    public function canHandleNotification_ReturnsFalseIfTheresNoGeneratorForNotification(): void
    {
        $this->notificationStrategy->addNotificationMessageGenerator(
            $this->generator,
            'some_inexistent_notification_name',
            self::GENERATOR_METHOD_NAME
        );

        self::assertFalse($this->notificationStrategy->canHandleNotification(new DummyNotification('some_hash')));
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function canHandleNotification_ReturnsFalseIfTheVoterReturnsFalse(): void
    {
        $notification = new DummyNotification('some_hash');

        $notificationName = ReflectionHelper::invokeProtectedMethod(
            $this->notificationStrategy,
            'getNotificationName',
            [$notification]
        );

        $this->notificationStrategy->addNotificationMessageGenerator(
            $this->generator,
            $notificationName,
            self::GENERATOR_METHOD_NAME,
            $this->voter,
            self::VOTER_METHOD_NAME
        );

        $this->voter->shouldReceive(self::VOTER_METHOD_NAME)
            ->with($notification)
            ->andReturn(false);

        self::assertFalse($this->notificationStrategy->canHandleNotification($notification));
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function canHandleNotification_ReturnsTrue(): void
    {
        $notification = new DummyNotification('some_hash');

        $notificationName = ReflectionHelper::invokeProtectedMethod(
            $this->notificationStrategy,
            'getNotificationName',
            [$notification]
        );

        $this->notificationStrategy->addNotificationMessageGenerator(
            $this->generator,
            $notificationName,
            self::GENERATOR_METHOD_NAME,
            $this->voter,
            self::VOTER_METHOD_NAME
        );

        $this->voter->shouldReceive(self::VOTER_METHOD_NAME)
            ->with($notification)
            ->andReturn(true);

        self::assertTrue($this->notificationStrategy->canHandleNotification($notification));
    }
}
