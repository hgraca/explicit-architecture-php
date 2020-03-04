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

/**
 * @small
 *
 * @internal
 */
final class ClassHelperUnitTest extends AbstractUnitTest
{
    /**
     * @test
     */
    public function extract_canonical_class_name(): void
    {
        self::assertSame('ClassHelperUnitTest', ClassHelper::extractCanonicalClassName(__CLASS__));
    }

    /**
     * @test
     */
    public function extract_canonical_method_name(): void
    {
        self::assertSame(
            'extract_canonical_method_name',
            ClassHelper::extractCanonicalMethodName(__METHOD__)
        );
    }
}
