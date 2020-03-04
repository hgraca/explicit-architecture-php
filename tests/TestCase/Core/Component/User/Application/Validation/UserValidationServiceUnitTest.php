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

namespace Acme\App\Test\TestCase\Core\Component\User\Application\Validation;

use Acme\App\Core\Component\User\Application\Validation\UserValidationService;
use Acme\App\Infrastructure\Validation\PhoneNumber\LibPhoneNumber\PhoneNumberValidator;
use Acme\App\Test\Framework\AbstractUnitTest;
use libphonenumber\PhoneNumberUtil;

/**
 * @small
 *
 * @internal
 */
final class UserValidationServiceUnitTest extends AbstractUnitTest
{
    /**
     * @var UserValidationService
     */
    private $object;

    public function __construct()
    {
        parent::__construct();

        $this->object = new UserValidationService(new PhoneNumberValidator(PhoneNumberUtil::getInstance(), 'NL'));
    }

    /**
     * @test
     */
    public function validate_username(): void
    {
        $test = 'username';

        self::assertSame($test, $this->object->validateUsername($test));
    }

    /**
     * @test
     */
    public function validate_username_empty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The username can not be empty.');
        $this->object->validateUsername(null);
    }

    /**
     * @test
     */
    public function validate_username_invalid(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The username must contain only lowercase latin characters and underscores.');
        $this->object->validateUsername('INVALID');
    }

    /**
     * @test
     */
    public function validate_password(): void
    {
        $test = 'password';

        self::assertSame($test, $this->object->validatePassword($test));
    }

    /**
     * @test
     */
    public function validate_password_empty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The password can not be empty.');
        $this->object->validatePassword(null);
    }

    /**
     * @test
     */
    public function validate_password_invalid(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The password must be at least 6 characters long.');
        $this->object->validatePassword('12345');
    }

    /**
     * @test
     */
    public function validate_email(): void
    {
        $test = '@';

        self::assertSame($test, $this->object->validateEmail($test));
    }

    /**
     * @test
     */
    public function validate_email_empty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The email can not be empty.');
        $this->object->validateEmail(null);
    }

    /**
     * @test
     */
    public function validate_email_invalid(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The email should look like a real email.');
        $this->object->validateEmail('invalid');
    }

    /**
     * @test
     */
    public function validate_full_name(): void
    {
        $test = 'Full Name';

        self::assertSame($test, $this->object->validateFullName($test));
    }

    /**
     * @test
     */
    public function validate_full_name_empty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The full name can not be empty.');
        $this->object->validateFullName(null);
    }
}
