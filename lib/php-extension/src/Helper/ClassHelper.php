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

final class ClassHelper extends AbstractStaticClass
{
    public static function extractCanonicalClassName(string $classFqcn): string
    {
        return mb_substr($classFqcn, mb_strrpos($classFqcn, '\\') + 1);
    }

    public static function extractCanonicalMethodName(string $methodFqcn): string
    {
        return mb_substr($methodFqcn, mb_strrpos($methodFqcn, '::') + 2);
    }

    /**
     * Converts 'TABLE_NAME', 'Table_NaMe', 'table_name' to 'TableName'.
     */
    public static function toStudlyCase(string $sentence): string
    {
        return str_replace([' ', '_', '-'], '', ucwords(mb_strtolower($sentence), ' _-'));
    }

    /**
     * Converts 'TABLE_NAME', 'Table_NaMe', 'table_name' to 'tableName'.
     */
    public static function toCamelCase(string $sentence): string
    {
        return lcfirst(self::toStudlyCase($sentence));
    }
}
