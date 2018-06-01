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

namespace Acme\App\Infrastructure\Notification\Client\Sms;

use Acme\App\Core\Port\Notification\Client\Sms\Sms;
use Acme\App\Core\Port\Notification\Client\Sms\SmsNotifierInterface;
use Acme\App\Core\Port\Validation\PhoneNumber\PhoneNumberCouldNotBeParsedException;
use Acme\App\Core\Port\Validation\PhoneNumber\PhoneNumberException;
use Acme\App\Core\Port\Validation\PhoneNumber\PhoneNumberValidatorInterface;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

/**
 * @author Nicolae Nichifor
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
abstract class AbstractClient implements SmsNotifierInterface
{
    /**
     * @var PhoneNumberValidatorInterface
     */
    private $phoneNumberValidator;

    /**
     * @var PhoneNumberUtil
     */
    private $phoneNumberUtil;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $defaultDestination;

    public function __construct(
        PhoneNumberValidatorInterface $phoneNumberValidator,
        PhoneNumberUtil $phoneNumberUtil,
        string $countryCode,
        string $defaultDestination = null
    ) {
        $this->phoneNumberValidator = $phoneNumberValidator;
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->countryCode = $countryCode;
        $this->defaultDestination = $defaultDestination;
    }

    /**
     * @throws PhoneNumberException
     */
    public function sendNotification(Sms $smsNotification): void
    {
        $phoneNumber = $this->formatToE164($this->defaultDestination ?? $smsNotification->getPhoneNumber());

        $this->triggerNotification($phoneNumber, $smsNotification->getContent());
    }

    abstract public function triggerNotification(string $phoneNumber, string $content): void;

    /**
     * {@inheritdoc}
     *
     * @throws PhoneNumberException
     */
    private function formatToE164(string $phoneNumber): string
    {
        $phoneNumberObject = $this->parsePhoneNumberOrThrowException($phoneNumber);

        return $this->phoneNumberUtil->format($phoneNumberObject, PhoneNumberFormat::E164);
    }

    /**
     * @throws PhoneNumberException
     */
    private function parsePhoneNumberOrThrowException(string $phoneNumber): PhoneNumber
    {
        $this->phoneNumberValidator->validate($phoneNumber);

        try {
            return $this->phoneNumberUtil->parse($phoneNumber, $this->countryCode);
        } catch (NumberParseException $exception) {
            throw new PhoneNumberCouldNotBeParsedException($phoneNumber);
        }
    }
}
