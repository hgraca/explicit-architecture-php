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

namespace Acme\App\Test\TestCase\Core\Component\Blog\Application\Service;

use Acme\App\Core\Component\Blog\Application\Query\FindHighestPostSlugSuffixQueryInterface;
use Acme\App\Core\Component\Blog\Application\Query\PostSlugExistsQueryInterface;
use Acme\App\Core\Component\Blog\Application\Repository\PostRepositoryInterface;
use Acme\App\Core\Component\Blog\Application\Service\PostService;
use Acme\App\Core\Component\Blog\Domain\Entity\Post;
use Acme\App\Core\Component\User\Domain\Entity\User;
use Acme\App\Test\Framework\AbstractUnitTest;
use Mockery;
use Mockery\MockInterface;

class PostServiceUnitTest extends AbstractUnitTest
{
    /**
     * @var MockInterface|PostRepositoryInterface
     */
    private $postRepositorySpy;

    /**
     * @var MockInterface|PostSlugExistsQueryInterface
     */
    private $postSlugExistsQuerySpy;

    /**
     * @var MockInterface|FindHighestPostSlugSuffixQueryInterface
     */
    private $findHighestPostSlugSuffixQuerySpy;

    /**
     * @var PostService
     */
    private $postService;

    public function setUp(): void
    {
        $this->postRepositorySpy = Mockery::spy(PostRepositoryInterface::class);
        $this->postSlugExistsQuerySpy = Mockery::spy(PostSlugExistsQueryInterface::class);
        $this->findHighestPostSlugSuffixQuerySpy = Mockery::spy(FindHighestPostSlugSuffixQueryInterface::class);

        $this->postService = new PostService(
            $this->postRepositorySpy,
            $this->postSlugExistsQuerySpy,
            $this->findHighestPostSlugSuffixQuerySpy
        );
    }

    /**
     * @test
     */
    public function create_if_slug_does_not_exist_do_not_post_fix(): void
    {
        $title = 'Some Interesting Title';

        $post = new Post();
        $post->setTitle($title);
        $slug = $post->getSlug();

        $this->postSlugExistsQuerySpy->shouldReceive('execute')->once()->with($slug)->andReturn(false);
        $this->postRepositorySpy->shouldReceive('upsert')->once()->with(
            Mockery::on(
                function (Post $post) use ($slug) {
                    return $post->getSlug() === $slug;
                }
            )
        );

        $this->postService->create($post, User::constructWithoutPassword('a', 'b', 'c', 'd'));
    }

    /**
     * @test
     */
    public function create_if_slug_exists_post_fix_it(): void
    {
        $title = 'Some Interesting Title';
        $maxSuffix = 21;

        $post = new Post();
        $post->setTitle($title);
        $slug = $post->getSlug();

        $this->postSlugExistsQuerySpy->shouldReceive('execute')->once()->with($slug)->andReturn(true);
        $this->findHighestPostSlugSuffixQuerySpy->shouldReceive('execute')->once()->with($slug)->andReturn($maxSuffix);
        $this->postRepositorySpy->shouldReceive('upsert')->once()->with(
            Mockery::on(
                function (Post $post) use ($slug, $maxSuffix) {
                    return $post->getSlug() === $slug . '-' . ++$maxSuffix;
                }
            )
        );

        $this->postService->create($post, User::constructWithoutPassword('a', 'b', 'c', 'd'));
    }
}
