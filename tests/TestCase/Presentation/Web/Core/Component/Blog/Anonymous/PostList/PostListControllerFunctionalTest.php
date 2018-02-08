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
use Acme\App\Test\Framework\AbstractFunctionalTest;

/**
 * Functional test for the controllers defined inside PostListController.
 *
 * See https://symfony.com/doc/current/book/testing.html#functional-tests
 *
 * Execute the application tests using this command (requires PHPUnit to be installed):
 *
 *     $ cd your-symfony-project/
 *     $ make test
 */
class PostListControllerFunctionalTest extends AbstractFunctionalTest
{
    /**
     * @test
     */
    public function get_html(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/blog/posts');

        $this->assertCount(
            Post::NUM_ITEMS,
            $crawler->filter('article.post'),
            'The homepage does not display the right number of posts.'
        );
    }

    /**
     * @test
     */
    public function get_rss(): void
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
            'The xml file does not display the right number of posts.'
        );
    }

    /**
     * @test
     * @dataProvider provideLimit
     */
    public function search_json_finds_all_posts(?int $limit, $expectedCount): void
    {
        $query = 'Lo';
        $limitString = $limit ? "&l=$limit" : '';
        $client = static::createClient();
        $client->request(
            'GET',
            "/en/blog/posts/search?q=$query" . $limitString,
            [],
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $this->assertSame(
            'application/json',
            $client->getResponse()->headers->get('Content-Type')
        );

        $postList = \json_decode($client->getResponse()->getContent(), true);

        $this->assertCount(
            $expectedCount,
            $postList,
            'The json file does not display the right number of posts.'
        );
    }

    public function provideLimit(): array
    {
        return [
            [null, 5],
            [2, 2],
        ];
    }
}
