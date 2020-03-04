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

namespace Acme\App\Test\TestCase\Core\Component\Blog\Post;

use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Test\Framework\AbstractUnitTest;

/**
 * @small
 *
 * @internal
 */
final class PostUnitTest extends AbstractUnitTest
{
    /**
     * @test
     * @dataProvider provideSuffixes
     */
    public function postfix_slug(string $suffix): void
    {
        $title = 'Some Interesting Title';

        $post = new Post();
        $post->setTitle($title);
        $slug = $post->getSlug();

        $post->postfixSlug($suffix);

        self::assertEquals($post->getSlug(), $slug . '-' . ltrim($suffix, '-'));
    }

    public function provideSuffixes(): array
    {
        return [
            ['15'],
            ['-15'],
        ];
    }
}
