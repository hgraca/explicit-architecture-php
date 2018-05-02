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

namespace Acme\PhpExtension\Helper;

use Acme\PhpExtension\AbstractStaticClass;

final class StringHelper extends AbstractStaticClass
{
    /**
     * Returns true if this string contains with the given needle.
     * If the needle is empty, it always returns true.
     */
    public static function contains(string $needle, string $haystack): bool
    {
        return $needle === '' || mb_strpos($haystack, $needle) !== false;
    }
}
