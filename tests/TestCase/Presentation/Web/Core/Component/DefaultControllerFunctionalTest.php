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

namespace Acme\App\Test\TestCase\Presentation\Web\Core\Component;

use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Test\Framework\AbstractFunctionalTest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Functional test that implements a "smoke test" of all the public and secure
 * URLs of the application.
 * See https://symfony.com/doc/current/best_practices/tests.html#functional-tests.
 *
 * Execute the application tests using this command (requires PHPUnit to be installed):
 *
 *     $ cd your-symfony-project/
 *     $ make test
 *
 * @large
 */
final class DefaultControllerFunctionalTest extends AbstractFunctionalTest
{
    /**
     * @test
     *
     * PHPUnit's data providers allow to execute the same tests repeated times
     * using a different set of data each time.
     * See https://symfony.com/doc/current/cookbook/form/unit_testing.html#testing-against-different-sets-of-data.
     *
     * @dataProvider getPublicUrls
     */
    public function public_urls(string $url): void
    {
        $this->getClient()->request('GET', $url);

        $this->assertResponseStatusCode(
            Response::HTTP_OK,
            $this->getClient(),
            sprintf('The %s public URL does not load correctly.', $url)
        );
    }

    /**
     * @test
     *
     * A good practice for tests is to not use the service container, to make
     * them more robust. However, in this example we must access to the container
     * to get the entity manager and make a database query. The reason is that
     * blog post fixtures are randomly generated and there's no guarantee that
     * a given blog post slug will be available.
     */
    public function public_blog_post(): void
    {
        $blogPost = $this->findAPost();
        $this->getClient()->request('GET', sprintf('/en/blog/posts/%s', $blogPost->getSlug()));

        self::assertResponseStatusCode(Response::HTTP_OK, $this->getClient());
    }

    /**
     * @test
     *
     * The application contains a lot of secure URLs which shouldn't be
     * publicly accessible. This tests ensures that whenever a user tries to
     * access one of those pages, a redirection to the login form is performed.
     *
     * @dataProvider getSecureUrls
     */
    public function secure_urls(string $url): void
    {
        $post = $this->findAPost();
        $postId = $post->getId();

        $this->getClient()->request('GET', sprintf($url, (string) $postId));

        self::assertResponseStatusCode(Response::HTTP_FOUND, $this->getClient());
        $this->assertSame(
            'http://localhost/en/login',
            $this->getClient()->getResponse()->getTargetUrl(),
            sprintf('The %s secure URL redirects to the login form.', $url)
        );
    }

    public function getPublicUrls()
    {
        yield ['/'];
        yield ['/en/blog/posts'];
        yield ['/en/login'];
    }

    public function getSecureUrls()
    {
        yield ['/en/admin/posts'];
        yield ['/en/admin/posts/new'];
        yield ['/en/admin/posts/%s'];
        yield ['/en/admin/posts/%s/edit'];
    }

    private function findAPost(): Post
    {
        $dqlQuery = $this->getDqlQueryBuilder()->create(Post::class)->setMaxResults(1)->build();

        return $this->getQueryService()->query($dqlQuery)->getSingleResult();
    }

    private function getDqlQueryBuilder(): DqlQueryBuilderInterface
    {
        return $this->getService(DqlQueryBuilderInterface::class);
    }

    private function getQueryService(): QueryServiceRouterInterface
    {
        return $this->getService(QueryServiceRouterInterface::class);
    }
}
