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

namespace Acme\PhpExtension\Test\Helper;

use Acme\PhpExtension\Helper\StringHelper;
use Acme\PhpExtension\Test\AbstractUnitTest;

final class StringHelperUnitTest extends AbstractUnitTest
{
    /**
     * @test
     * @dataProvider provideStrings
     */
    public function contains_finds_needle_when_its_there(string $needle, string $haystack, bool $expectedResult): void
    {
        self::assertEquals($expectedResult, StringHelper::contains($needle, $haystack));
    }

    public function provideStrings(): array
    {
        return [
            ['', 'beginning to ending', true],
            ['beginning', 'beginning to ending', true],
            ['to', 'beginning to ending', true],
            ['ending', 'beginning to ending', true],
            ['unexistent', 'beginning to ending', false],
        ];
    }
}
