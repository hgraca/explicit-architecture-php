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

namespace Acme\App\Test\TestCase\Presentation\Web\Core\Component\Component\Blog\Admin\Post;

use Acme\App\Core\Component\Blog\Domain\Entity\Post;
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
class PostControllerTest extends WebTestCase
{
    /**
     * @dataProvider getUrlsForRegularUsers
     */
    public function testAccessDeniedForRegularUsers($httpMethod, $url): void
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
        yield ['GET', '/en/admin/posts/1'];
        yield ['GET', '/en/admin/posts/1/edit'];
        yield ['POST', '/en/admin/posts/1/delete'];
    }

    public function testAdminGetPost(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'jane_admin',
            'PHP_AUTH_PW' => 'kitten',
        ]);
        $client->request('GET', '/en/admin/posts/1');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * This test changes the database contents by editing a blog post. However,
     * thanks to the DAMADoctrineTestBundle and its PHPUnit listener, all changes
     * to the database are rolled back when this test completes. This means that
     * all the application tests begin with the same database contents.
     */
    public function testAdminEditPost(): void
    {
        $newBlogPostTitle = 'Blog Post Title ' . mt_rand();

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'jane_admin',
            'PHP_AUTH_PW' => 'kitten',
        ]);
        $crawler = $client->request('GET', '/en/admin/posts/1/edit');
        $form = $crawler->selectButton('Save changes')->form([
            'post[title]' => $newBlogPostTitle,
        ]);
        $client->submit($form);

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        /** @var Post $post */
        $post = $client->getContainer()->get('doctrine')->getRepository(Post::class)->find(1);
        $this->assertSame($newBlogPostTitle, $post->getTitle());
    }

    /**
     * This test changes the database contents by deleting a blog post. However,
     * thanks to the DAMADoctrineTestBundle and its PHPUnit listener, all changes
     * to the database are rolled back when this test completes. This means that
     * all the application tests begin with the same database contents.
     */
    public function testAdminDeletePost(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'jane_admin',
            'PHP_AUTH_PW' => 'kitten',
        ]);
        $crawler = $client->request('GET', '/en/admin/posts/1');
        $client->submit($crawler->filter('#delete-form')->form());

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        $post = $client->getContainer()->get('doctrine')->getRepository(Post::class)->find(1);
        $this->assertNull($post);
    }
}
