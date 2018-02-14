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

namespace Acme\App\Test\TestCase\Presentation\Web\Core\Component\Component\Blog\Admin\PostList;

use Acme\App\Core\Component\Blog\Domain\Entity\Post;
use Acme\App\Test\Fixture\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Functional test for the controllers defined inside the PostListController used
 * for managing the blog in the backend.
 *
 * See https://symfony.com/doc/current/book/testing.html#functional-tests
 *
 * Whenever you test resources protected by a firewall, consider using the
 * technique explained in:
 * https://symfony.com/doc/current/cookbook/testing/http_authentication.html
 *
 * Execute the application tests using this command (requires PHPUnit to be installed):
 *
 *     $ cd your-symfony-project/
 *     $ ./vendor/bin/phpunit
 */
class PostListControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @dataProvider getUrlsForRegularUsers
     */
    public function testAccessDeniedForRegularUsers(string $httpMethod, string $url): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'john_user',
            'PHP_AUTH_PW' => 'kitten',
        ]);

        $client->request($httpMethod, $url);
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function getUrlsForRegularUsers()
    {
        yield ['GET', '/en/admin/posts'];
    }

    public function testAdminBackendHomePage(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'jane_admin',
            'PHP_AUTH_PW' => 'kitten',
        ]);

        $crawler = $client->request('GET', '/en/admin/posts/');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->assertGreaterThanOrEqual(
            1,
            $crawler->filter('body#admin_post_index #main tbody tr')->count(),
            'The backend homepage displays all the available posts.'
        );
    }

    /**
     * This test changes the database contents by creating a new blog post. However,
     * thanks to the DAMADoctrineTestBundle and its PHPUnit listener, all changes
     * to the database are rolled back when this test completes. This means that
     * all the application tests begin with the same database contents.
     */
    public function testAdminNewPost(): void
    {
        $postTitle = 'Blog Post Title ' . mt_rand();
        $postSummary = $this->generateRandomString(255);
        $postContent = $this->generateRandomString(1024);

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'jane_admin',
            'PHP_AUTH_PW' => 'kitten',
        ]);
        $crawler = $client->request('GET', '/en/admin/posts/new');
        $form = $crawler->selectButton('Create post')->form([
            'post[title]' => $postTitle,
            'post[summary]' => $postSummary,
            'post[content]' => $postContent,
        ]);
        $client->submit($form);

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        /** @var Post $post */
        $post = $client->getContainer()->get('doctrine')->getRepository(Post::class)->findOneBy([
            'title' => $postTitle,
        ]);
        $this->assertNotNull($post);
        $this->assertSame($postSummary, $post->getSummary());
        $this->assertSame($postContent, $post->getContent());
    }

    private function generateRandomString(int $length): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return mb_substr(str_shuffle(str_repeat($chars, (int) ceil($length / mb_strlen($chars)))), 1, $length);
    }
}
