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

namespace Acme\PhpExtension\Test\String;

use Acme\PhpExtension\String\Slugger;
use Acme\PhpExtension\Test\AbstractUnitTest;

/**
 * Unit test for the application utils.
 *
 * See https://symfony.com/doc/current/book/testing.html#unit-tests
 *
 * Execute the application tests using this command (requires PHPUnit to be installed):
 *
 *     $ cd your-symfony-project/
 *     $ ./vendor/bin/phpunit
 */
class SluggerUnitTest extends AbstractUnitTest
{
    /**
     * @dataProvider getSlugs
     */
    public function testSlugify(string $string, string $slug): void
    {
        $this->assertSame($slug, Slugger::slugify($string));
    }

    public function getSlugs()
    {
        yield ['Lorem Ipsum', 'lorem-ipsum'];
        yield ['  Lorem Ipsum  ', 'lorem-ipsum'];
        yield [' lOrEm  iPsUm  ', 'lorem-ipsum'];
        yield ['!Lorem Ipsum!', '!lorem-ipsum!'];
        yield ['lorem-ipsum', 'lorem-ipsum'];
        yield ['lorem 日本語 ipsum', 'lorem-日本語-ipsum'];
        yield ['lorem русский язык ipsum', 'lorem-русский-язык-ipsum'];
        yield ['lorem العَرَبِيَّة‎‎ ipsum', 'lorem-العَرَبِيَّة‎‎-ipsum'];
    }
}
