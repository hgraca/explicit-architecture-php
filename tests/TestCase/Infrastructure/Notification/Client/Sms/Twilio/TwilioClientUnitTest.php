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

namespace Acme\App\Test\TestCase\Infrastructure\Notification\Client\Sms\Twilio;

use Acme\App\Core\Port\Notification\Client\Sms\Sms;
use Acme\App\Core\Port\Validation\PhoneNumber\PhoneNumberException;
use Acme\App\Infrastructure\Notification\Client\Sms\Twilio\TwilioClient;
use Acme\App\Infrastructure\Validation\PhoneNumber\LibPhoneNumber\PhoneNumberValidator;
use Acme\App\Test\Framework\AbstractUnitTest;
use libphonenumber\PhoneNumberUtil;
use Mockery;
use Mockery\MockInterface;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\Rest\Api\V2010\AccountContext;
use Twilio\Rest\Client as TwilioRestClient;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Nicolae Nichifor
 *
 * @small
 */
final class TwilioClientUnitTest extends AbstractUnitTest
{
    private const FROM_PHONE_NUMBER = '+31631769216';
    private const TO_PHONE_NUMBER = '+31631769217';
    private const SMS_CONTENT = 'some content ...';

    /**
     * @var MockInterface|AccountContext
     */
    private $account;

    /**
     * @var MockInterface|MessageList
     */
    private $messageList;

    /**
     * @var MockInterface|TwilioRestClient
     */
    private $twilioClient;

    /**
     * @var MockInterface|MessageInstance
     */
    private $response;

    /**
     * @var TwilioClient
     */
    private $twilioClientAdapter;

    public function setUp(): void
    {
        $this->messageList = Mockery::mock(MessageList::class);
        $this->account = Mockery::mock(AccountContext::class);
        $this->account->messages = $this->messageList;
        $this->twilioClient = Mockery::mock(TwilioRestClient::class);
        $this->response = Mockery::mock(MessageInstance::class);
        $this->twilioClientAdapter = new TwilioClient(
            new PhoneNumberValidator(PhoneNumberUtil::getInstance(), 'NL'),
            PhoneNumberUtil::getInstance(),
            'NL',
            $this->twilioClient,
            self::FROM_PHONE_NUMBER
        );
    }

    /**
     * @test
     *
     * @throws PhoneNumberException
     */
    public function sendNotification(): void
    {
        $this->response->status = 'queued';
        $this->messageList->shouldReceive('create')->once()->with(
            self::TO_PHONE_NUMBER,
            [
                'from' => self::FROM_PHONE_NUMBER,
                'body' => self::SMS_CONTENT,
            ]
        )->andReturn($this->response);
        $this->twilioClient->shouldReceive('getAccount')->once()->andReturn($this->account);

        $this->twilioClientAdapter->sendNotification(
            new Sms(self::SMS_CONTENT, self::TO_PHONE_NUMBER)
        );
    }

    /**
     * @test
     * @expectedException \Acme\App\Core\Port\Notification\Client\Sms\Exception\SmsNotifierException
     *
     * @throws PhoneNumberException
     */
    public function sendNotification_transforms_twilio_exception(): void
    {
        $this->twilioClient->shouldReceive('getAccount')->once()->andThrow(TwilioException::class);

        $this->twilioClientAdapter->sendNotification(new Sms(self::SMS_CONTENT, self::TO_PHONE_NUMBER));
    }
}
