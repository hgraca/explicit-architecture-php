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

namespace Acme\App\Test\TestCase\Core\Port\Notification\Client\Email;

use Acme\App\Core\Port\Notification\Client\Email\EmailAddress;
use Acme\App\Test\Framework\AbstractUnitTest;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Marijn Koesen
 */
class EmailAddressUnitTest extends AbstractUnitTest
{
    /**
     * @test
     * @dataProvider getAddresses
     */
    public function getEmail_and_getName_work_as_expected(string $address, ?string $name = null): void
    {
        $mailAddress = new EmailAddress($address, $name);
        $this->assertEquals($address, $mailAddress->getEmail());
        $this->assertEquals($name, $mailAddress->getName());
    }

    public function getAddresses(): array
    {
        return [
            ['john', null],
            ['john', 'John'],
            ['john@doe.ext', null],
            ['user@[IPv6:2001:db8:1ff::a0b:dbd0]', null],
            ['"qupotes"@doe.ext', null],
            ['"quotes.and.@"@doe.ext', null],
            ['" "@doe.ext', null],
            ['tld@nl', null],
            ['john@doe.ext', null],
            ['admin@hostname', null],
            ['!#$%&\'*+-/=?^_`{}|~@doe.ext', null],
            ['"()<>[]:,;@\\\"!#$%&\'*+-/=?^_`{}| ~.a"@doe.ext', null],
            ['üñîçøðé@üñîçøðé.com', null],
            ['john@doe.ext', 'John'],
        ];
    }

    /**
     * @test
     * @dataProvider getToStringData
     */
    public function toString_allows_casting_to_string(?string $name, string $email, string $result): void
    {
        $mailAddress = new EmailAddress($email, (string) $name);
        $this->assertSame($result, (string) $mailAddress);
    }

    public function getToStringData(): array
    {
        return [
            ['john', 'john@doe.com', 'john <john@doe.com>'],
            [null, 'john@doe.com', 'john@doe.com'],
        ];
    }
}
