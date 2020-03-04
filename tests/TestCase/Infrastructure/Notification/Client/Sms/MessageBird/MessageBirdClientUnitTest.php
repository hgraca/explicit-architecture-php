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

namespace Acme\App\Test\TestCase\Infrastructure\Notification\Client\Sms\MessageBird;

use Acme\App\Core\Port\Notification\Client\Sms\Sms;
use Acme\App\Core\Port\Validation\PhoneNumber\PhoneNumberException;
use Acme\App\Infrastructure\Notification\Client\Sms\MessageBird\MessageBirdClient;
use Acme\App\Infrastructure\Validation\PhoneNumber\LibPhoneNumber\PhoneNumberValidator;
use Acme\App\Test\Framework\AbstractUnitTest;
use Hamcrest\Core\IsEqual;
use libphonenumber\PhoneNumberUtil;
use MessageBird\Client;
use MessageBird\Exceptions\RequestException;
use MessageBird\Objects\Message;
use MessageBird\Resources\Messages;
use Mockery;
use Mockery\MockInterface;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Winfred Peereboom
 *
 * @small
 *
 * @internal
 */
final class MessageBirdClientUnitTest extends AbstractUnitTest
{
    private const FROM_PHONE_NUMBER = '+390656470420';
    private const TO_PHONE_NUMBER = '+393396007250';
    private const TO_DEFAULT_PHONE_NUMBER = '+393396007251';
    private const SMS_CONTENT = 'New job invite!';

    /**
     * @var MessageBirdClient
     */
    private $messageBirdClientAdapter;

    /**
     * @var MockInterface|Client
     */
    private $messageBirdClient;

    protected function setUp(): void
    {
        $this->messageBirdClient = Mockery::mock(Client::class);
        $this->messageBirdClientAdapter = new MessageBirdClient(
            new PhoneNumberValidator(PhoneNumberUtil::getInstance(), 'NL'),
            PhoneNumberUtil::getInstance(),
            'NL',
            $this->messageBirdClient,
            self::FROM_PHONE_NUMBER
        );
    }

    /**
     * @test
     *
     * @throws PhoneNumberException
     */
    public function send_notification(): void
    {
        $messages = $this->mockSuccessfulMessage(self::TO_PHONE_NUMBER);
        $this->messageBirdClient->messages = $messages;

        $this->messageBirdClientAdapter->sendNotification(
            new Sms(
                self::SMS_CONTENT,
                self::TO_PHONE_NUMBER
            )
        );
    }

    /**
     * @test
     *
     * @throws PhoneNumberException
     */
    public function send_notification_transforms_message_bird_exception(): void
    {
        $this->expectException(\Acme\App\Core\Port\Notification\Client\Sms\Exception\SmsNotifierException::class);

        $messages = $this->mockFailedMessage();

        $this->messageBirdClient->messages = $messages;

        $this->messageBirdClientAdapter->sendNotification(
            new Sms(
                self::SMS_CONTENT,
                self::TO_PHONE_NUMBER
            )
        );
    }

    /**
     * @test
     *
     * @throws PhoneNumberException
     */
    public function send_notification_default_number_overrides_notification(): void
    {
        $messages = $this->mockSuccessfulMessage(self::TO_DEFAULT_PHONE_NUMBER);
        $this->messageBirdClient->messages = $messages;

        $client = new MessageBirdClient(
            new PhoneNumberValidator(PhoneNumberUtil::getInstance(), 'NL'),
            PhoneNumberUtil::getInstance(),
            'NL',
            $this->messageBirdClient,
            self::FROM_PHONE_NUMBER,
            self::TO_DEFAULT_PHONE_NUMBER
        );

        $client->sendNotification(
            new Sms(
                self::SMS_CONTENT,
                self::TO_PHONE_NUMBER
            )
        );
    }

    private function mockSuccessfulMessage(string $to): Messages
    {
        $message = new Message();
        $message->originator = self::FROM_PHONE_NUMBER;
        $message->recipients = [$to];
        $message->body = self::SMS_CONTENT;

        $messages = Mockery::mock(Messages::class);
        $messages->shouldReceive('create')
            ->once()
            ->with(IsEqual::equalTo($message));

        return $messages;
    }

    private function mockFailedMessage(): Messages
    {
        $message = new Message();
        $message->originator = self::FROM_PHONE_NUMBER;
        $message->recipients = [self::TO_PHONE_NUMBER];
        $message->body = self::SMS_CONTENT;

        $messages = Mockery::mock(Messages::class);
        $messages->shouldReceive('create')
            ->once()
            ->with(IsEqual::equalTo($message))
            ->andThrow(RequestException::class);

        return $messages;
    }
}
