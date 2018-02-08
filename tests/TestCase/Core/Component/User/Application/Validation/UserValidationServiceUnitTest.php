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
use Acme\App\Test\Framework\AbstractUnitTest;

class UserValidationServiceUnitTest extends AbstractUnitTest
{
    /**
     * @var UserValidationService
     */
    private $object;

    public function __construct()
    {
        parent::__construct();

        $this->object = new UserValidationService();
    }

    public function testValidateUsername(): void
    {
        $test = 'username';

        $this->assertSame($test, $this->object->validateUsername($test));
    }

    public function testValidateUsernameEmpty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The username can not be empty.');
        $this->object->validateUsername(null);
    }

    public function testValidateUsernameInvalid(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The username must contain only lowercase latin characters and underscores.');
        $this->object->validateUsername('INVALID');
    }

    public function testValidatePassword(): void
    {
        $test = 'password';

        $this->assertSame($test, $this->object->validatePassword($test));
    }

    public function testValidatePasswordEmpty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The password can not be empty.');
        $this->object->validatePassword(null);
    }

    public function testValidatePasswordInvalid(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The password must be at least 6 characters long.');
        $this->object->validatePassword('12345');
    }

    public function testValidateEmail(): void
    {
        $test = '@';

        $this->assertSame($test, $this->object->validateEmail($test));
    }

    public function testValidateEmailEmpty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The email can not be empty.');
        $this->object->validateEmail(null);
    }

    public function testValidateEmailInvalid(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The email should look like a real email.');
        $this->object->validateEmail('invalid');
    }

    public function testValidateFullName(): void
    {
        $test = 'Full Name';

        $this->assertSame($test, $this->object->validateFullName($test));
    }

    public function testValidateEmailFullName(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The full name can not be empty.');
        $this->object->validateFullName(null);
    }
}
