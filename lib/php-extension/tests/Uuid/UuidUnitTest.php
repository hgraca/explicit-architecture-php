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

namespace Acme\PhpExtension\Test\Uuid;

use Acme\PhpExtension\Test\AbstractUnitTest;
use Acme\PhpExtension\Uuid\Uuid;

/**
 * @small
 *
 * @internal
 */
final class UuidUnitTest extends AbstractUnitTest
{
    /**
     * @test
     */
    public function construct_throws_exception_if_invalid_uuid_string(): void
    {
        $this->expectException(\Acme\PhpExtension\Uuid\InvalidUuidStringException::class);

        new Uuid('foo');
    }

    /**
     * @test
     * @dataProvider provideUuid
     */
    public function is_valid(string $uuid, bool $expectedValue): void
    {
        self::assertEquals($expectedValue, Uuid::isValid($uuid));
    }

    public function provideUuid(): array
    {
        return [
            ['7a980ca1-5504-4b8c-93be-605cb76700ec', true],
            ['foo', false],
        ];
    }

    /**
     * @test
     */
    public function to_string_returns_correct_string(): void
    {
        $uuid = '7a980ca1-5504-4b8c-93be-605cb76700ec';

        self::assertEquals($uuid, (string) (new Uuid($uuid)));
    }
}
