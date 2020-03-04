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

use Acme\App\Presentation\Web\Core\Port\Paginator\PaginatorInterface;
use Acme\App\Test\Framework\AbstractFunctionalTest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Functional test for the controllers defined inside PostListController.
 *
 * See https://symfony.com/doc/current/book/testing.html#functional-tests
 *
 * Execute the application tests using this command (requires PHPUnit to be installed):
 *
 *     $ cd your-symfony-project/
 *     $ make test
 *
 * @large
 *
 * @internal
 */
final class PostListControllerFunctionalTest extends AbstractFunctionalTest
{
    /**
     * @test
     */
    public function get_html(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/blog/posts');

        self::assertResponseStatusCode(Response::HTTP_OK, $client);
        self::assertCount(
            PaginatorInterface::DEFAULT_MAX_ITEMS_PER_PAGE,
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

        self::assertResponseStatusCode(Response::HTTP_OK, $client);
        self::assertSame(
            'text/xml; charset=UTF-8',
            $client->getResponse()->headers->get('Content-Type')
        );

        self::assertCount(
            PaginatorInterface::DEFAULT_MAX_ITEMS_PER_PAGE,
            $crawler->filter('item'),
            'The xml file does not display the right number of posts.'
        );
    }

    /**
     * @test
     */
    public function get_has_the_expected_headers(): void
    {
        $client = static::createClient();
        $client->request('GET', '/en/blog/posts');
        $headers = $client->getResponse()->headers;

        self::assertSame('0', $headers->getCacheControlDirective('max-age'));
        self::assertTrue($headers->getCacheControlDirective('must-revalidate'));
        self::assertTrue($headers->getCacheControlDirective('no-cache'));
        self::assertTrue($headers->getCacheControlDirective('private'));
        self::assertSame('10', $headers->getCacheControlDirective('s-maxage'));
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

        self::assertSame(
            $expectedContentType = 'application/json',
            $actualContentType = $client->getResponse()->headers->get('Content-Type'),
            "The response content type does not match. Expected '$expectedContentType' got '$actualContentType'."
            . ' Response content: \'' . $client->getResponse()->getContent() . '\''
        );

        $postList = \json_decode($client->getResponse()->getContent(), true);

        self::assertCount(
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
