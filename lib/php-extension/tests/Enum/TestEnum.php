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

namespace Acme\PhpExtension\Test\Enum;

use Acme\PhpExtension\Enum\AbstractEnum;

/**
 * @method static TestEnum a()
 * @method bool   isA()
 * @method static TestEnum b()
 * @method bool   isB()
 * @method static TestEnum a1()
 * @method bool   isA1()
 * @method static TestEnum a3()
 * @method bool   isA3()
 * @method static TestEnum anull()
 * @method bool   isAnull()
 * @method static TestEnum nameWithUnderscore()
 * @method bool   isNameWithUnderscore()
 */
class TestEnum extends AbstractEnum
{
    const A = 'A';
    const B = 'BEE';
    const A1 = 1;
    const A3 = 3;
    const ANULL = null;
    const NAME_WITH_UNDERSCORE = true;
}
