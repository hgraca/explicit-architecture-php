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

namespace Acme\App\Test\TestCase\Presentation\Web\Core\Component\Blog\User\Comment;

use Acme\App\Presentation\Web\Core\Port\Router\UrlGeneratorInterface;
use Acme\App\Presentation\Web\Core\Port\Router\UrlType;
use Acme\App\Test\Framework\AbstractFunctionalTest;

/**
 * Functional test for the controllers defined inside CommentController.
 *
 * See https://symfony.com/doc/current/book/testing.html#functional-tests
 *
 * Execute the application tests using this command (requires PHPUnit to be installed):
 *
 *     $ cd your-symfony-project/
 *     $ ./vendor/bin/phpunit
 */
class CommentControllerFunctionalTest extends AbstractFunctionalTest
{
    /**
     * This test changes the database contents by creating a new comment. However,
     * thanks to the DAMADoctrineTestBundle and its PHPUnit listener, all changes
     * to the database are rolled back when this test completes. This means that
     * all the application tests begin with the same database contents.
     */
    public function testNewComment(): void
    {
        $client = $this->getClient([], [
            'PHP_AUTH_USER' => 'john_user',
            'PHP_AUTH_PW' => 'kitten',
        ]);
        $client->followRedirects();

        // Find first blog post
        $crawler = $client->request('GET', '/en/blog/posts');
        $postLink = $crawler->filter('article.post > h2 a')->link();

        $crawler = $client->click($postLink);

        $form = $crawler->selectButton('Publish comment')->form([
            'comment_form[content]' => 'Hi, Symfony!',
        ]);
        $crawler = $client->submit($form);

        $newComment = $crawler->filter('.post-comment')->first()->filter('div > p')->text();

        $this->assertSame('Hi, Symfony!', $newComment);
    }

    /**
     * This test changes the database contents by creating a new comment. However,
     * thanks to the DAMADoctrineTestBundle and its PHPUnit listener, all changes
     * to the database are rolled back when this test completes. This means that
     * all the application tests begin with the same database contents.
     */
    public function testNewComment_without_being_logged_in_redirects_to_login_page(): void
    {
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->getService(UrlGeneratorInterface::class);

        $client = static::createClient();
        $client->followRedirects();

        // Find first blog post
        $crawler = $client->request(
            'POST',
            '/en/blog/posts/some-post-slug-that-will-not-matter/comments',
            ['comment_form[content]' => 'Hi, Symfony!']
        );

        $this->assertSame(
            $urlGenerator->generateUrl('security_login', [], UrlType::absoluteUrl()),
            $crawler->getUri()
        );
    }
}
