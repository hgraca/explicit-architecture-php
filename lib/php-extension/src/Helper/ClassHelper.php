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

    public static function toStudlyCase(string $sentence): string
    {
        return self::removeAllSpaces(
            self::makeAllWordsUpperCaseFirst(
                self::makeLowCase(
                    self::separateCapitalizedWordsWithSpace(
                        self::separateWordsWithSpace($sentence)
                    )
                )
            )
        );
    }

    public static function toCamelCase(string $sentence): string
    {
        return lcfirst(self::toStudlyCase($sentence));
    }

    public static function toSnakeCase(string $sentence): string
    {
        $sentence = static::toStudlyCase($sentence);

        $replace = '$1' . '_' . '$2';

        return ctype_lower($sentence) ? $sentence : mb_strtolower(preg_replace('/(.)([A-Z])/', $replace, $sentence));
    }

    private static function separateWordsWithSpace(
        string $sentence,
        array $wordSeparatorList = ['-', '_', '.', ' ']
    ): string {
        return str_replace($wordSeparatorList, ' ', $sentence);
    }

    private static function makeAllWordsUpperCaseFirst(
        string $sentence,
        array $wordSeparatorList = ['-', '_', '.', ' ']
    ): string {
        return ucwords($sentence, implode('', $wordSeparatorList));
    }

    private static function makeLowCase(string $sentence): string
    {
        return mb_strtolower($sentence);
    }

    private static function removeAllSpaces(string $sentence): string
    {
        return str_replace([' '], '', $sentence);
    }

    private static function separateCapitalizedWordsWithSpace(string $sentence): string
    {
        return preg_replace('/([a-z])([A-Z])/', '$1 $2', $sentence);
    }
}
