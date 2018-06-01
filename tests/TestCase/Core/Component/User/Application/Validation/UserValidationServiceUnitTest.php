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
    public function validateUsername(): void
    {
        $test = 'username';

        $this->assertSame($test, $this->object->validateUsername($test));
    }

    /**
     * @test
     */
    public function validateUsername_empty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The username can not be empty.');
        $this->object->validateUsername(null);
    }

    /**
     * @test
     */
    public function validateUsername_invalid(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The username must contain only lowercase latin characters and underscores.');
        $this->object->validateUsername('INVALID');
    }

    /**
     * @test
     */
    public function validatePassword(): void
    {
        $test = 'password';

        $this->assertSame($test, $this->object->validatePassword($test));
    }

    /**
     * @test
     */
    public function validatePassword_empty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The password can not be empty.');
        $this->object->validatePassword(null);
    }

    /**
     * @test
     */
    public function validatePassword_invalid(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The password must be at least 6 characters long.');
        $this->object->validatePassword('12345');
    }

    /**
     * @test
     */
    public function validateEmail(): void
    {
        $test = '@';

        $this->assertSame($test, $this->object->validateEmail($test));
    }

    /**
     * @test
     */
    public function validateEmail_empty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The email can not be empty.');
        $this->object->validateEmail(null);
    }

    /**
     * @test
     */
    public function validateEmail_invalid(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The email should look like a real email.');
        $this->object->validateEmail('invalid');
    }

    /**
     * @test
     */
    public function validateFullName(): void
    {
        $test = 'Full Name';

        $this->assertSame($test, $this->object->validateFullName($test));
    }

    /**
     * @test
     */
    public function validateFullName_empty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The full name can not be empty.');
        $this->object->validateFullName(null);
    }
}
