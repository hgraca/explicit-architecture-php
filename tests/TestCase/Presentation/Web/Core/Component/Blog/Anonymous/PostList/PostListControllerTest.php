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

namespace Acme\App\Test\TestCase\Presentation\Web\Core\Component\Blog\Anonymous\PostList;

use Acme\App\Core\Component\Blog\Domain\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Functional test for the controllers defined inside PostListController.
 *
 * See https://symfony.com/doc/current/book/testing.html#functional-tests
 *
 * Execute the application tests using this command (requires PHPUnit to be installed):
 *
 *     $ cd your-symfony-project/
 *     $ ./vendor/bin/phpunit
 */
class PostListControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/blog/posts');

        $this->assertCount(
            Post::NUM_ITEMS,
            $crawler->filter('article.post'),
            'The homepage displays the right number of posts.'
        );
    }

    public function testRss(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/blog/posts/rss.xml');

        $this->assertSame(
            'text/xml; charset=UTF-8',
            $client->getResponse()->headers->get('Content-Type')
        );

        $this->assertCount(
            Post::NUM_ITEMS,
            $crawler->filter('item'),
            'The xml file displays the right number of posts.'
        );
    }
}
