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

use Acme\PhpExtension\Helper\ClassHelper;
use Acme\PhpExtension\Test\AbstractUnitTest;

final class ClassHelperUnitTest extends AbstractUnitTest
{
    /**
     * @test
     */
    public function extractCanonicalClassName(): void
    {
        self::assertSame('ClassHelperUnitTest', ClassHelper::extractCanonicalClassName(__CLASS__));
    }

    /**
     * @test
     */
    public function extractCanonicalMethodName(): void
    {
        self::assertSame(
            'extractCanonicalMethodName',
            ClassHelper::extractCanonicalMethodName(__METHOD__)
        );
    }

    /**
     * @test
     * @dataProvider provideStudlyTests
     */
    public function toStudlyCase(string $input, string $expectedOutput): void
    {
        self::assertSame($expectedOutput, ClassHelper::toStudlyCase($input));
    }

    public function provideStudlyTests(): array
    {
        return [
            ['TABLE_NAME', 'TableName'],
            ['Table_NaMe', 'TableName'],
            ['table_name', 'TableName'],
        ];
    }

    /**
     * @test
     * @dataProvider provideCamelCaseTests
     */
    public function toCamelCase(string $input, string $expectedOutput): void
    {
        self::assertSame($expectedOutput, ClassHelper::toCamelCase($input));
    }

    public function provideCamelCaseTests(): array
    {
        return [
            ['TABLE_NAME', 'tableName'],
            ['Table_NaMe', 'tableName'],
            ['table_name', 'tableName'],
        ];
    }
}
