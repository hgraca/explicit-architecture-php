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

namespace Acme\App\Infrastructure\Notification\Client\Sms\Twilio;

use Acme\App\Core\Port\Notification\Client\Sms\Exception\SmsNotifierException;
use Acme\App\Core\Port\Validation\PhoneNumber\PhoneNumberValidatorInterface;
use Acme\App\Infrastructure\Notification\Client\Sms\AbstractClient;
use libphonenumber\PhoneNumberUtil;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

/**
 * @author Nicolae Nichifor
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
final class TwilioClient extends AbstractClient
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $sender;

    public function __construct(
        PhoneNumberValidatorInterface $phoneNumberValidator,
        PhoneNumberUtil $phoneNumberUtil,
        string $countryCode,
        Client $client,
        string $sender,
        string $defaultDestination = null
    ) {
        parent::__construct($phoneNumberValidator, $phoneNumberUtil, $countryCode, $defaultDestination);
        $this->client = $client;
        $this->sender = $sender;
    }

    public function triggerNotification(string $phoneNumber, string $content): void
    {
        try {
            $this->client
                ->getAccount()
                ->messages
                ->create(
                    $phoneNumber,
                    [
                        'from' => $this->sender,
                        'body' => $content,
                    ]
                );
        } catch (TwilioException $e) {
            throw new SmsNotifierException($e->getMessage(), 0, $e);
        }
    }
}
