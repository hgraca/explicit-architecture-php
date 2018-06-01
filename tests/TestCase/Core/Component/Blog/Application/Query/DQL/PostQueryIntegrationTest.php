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

namespace Acme\App\Test\TestCase\Core\Component\Blog\Application\Query\DQL;

use Acme\App\Core\Component\Blog\Application\Query\PostQueryInterface;
use Acme\App\Core\Component\Blog\Application\Query\PostWithAuthorAndTagsDto;
use Acme\App\Core\Component\Blog\Application\Query\PostWithAuthorDto;
use Acme\App\Core\Component\Blog\Domain\Post\Comment\CommentId;
use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Component\Blog\Domain\Post\Tag\Tag;
use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;
use Acme\App\Test\Framework\AbstractIntegrationTest;

final class PostQueryIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @var DqlQueryBuilderInterface
     */
    private $dqlQueryBuilder;

    /**
     * @var QueryServiceRouterInterface
     */
    private $queryService;

    public function setUp(): void
    {
        $this->dqlQueryBuilder = self::getService(DqlQueryBuilderInterface::class);
        $this->queryService = self::getService(QueryServiceRouterInterface::class);
    }

    /**
     * @test
     */
    public function execute_gets_post_with_author_using_comment_id(): void
    {
        $post = $this->findAPost();
        $commentId = $post->getComments()[0]->getId();
        $this->clearDatabaseCache();

        $postDto = $this->getPostWithAuthorDtoUsingCommentId($commentId);

        self::assertSame($post->getSlug(), $postDto->getSlug());
        self::assertSame($this->findUser($post->getAuthorId())->getEmail(), $postDto->getAuthorEmail());
    }

    /**
     * @test
     */
    public function execute_gets_post_with_author_and_tags_using_post_id(): void
    {
        $post = $this->findAPost();
        $postId = $post->getId();
        $this->clearDatabaseCache();

        $postDto = $this->getPostWithAuthorAndTagsUsingPostId($postId);

        self::assertSame($post->getSlug(), $postDto->getSlug());
        $postTagList = array_map(
            function (Tag $tag) {
                return (string) $tag;
            },
            $post->getTags()->toArray()
        );
        sort($postTagList);
        $postDtoTagList = $postDto->getTagList();
        sort($postDtoTagList);
        self::assertSame($postTagList, $postDtoTagList);
        self::assertSame($this->findUser($post->getAuthorId())->getFullName(), $postDto->getAuthorFullName());
    }

    private function getPostWithAuthorAndTagsUsingPostId(PostId $postId): PostWithAuthorAndTagsDto
    {
        return $this->getPostQuery()
            ->includeAuthor()
            ->includeTags()
            ->execute($postId)
            ->hydrateSingleResultAs(PostWithAuthorAndTagsDto::class);
    }

    private function getPostWithAuthorDtoUsingCommentId(CommentId $commentId): PostWithAuthorDto
    {
        return $this->getPostQuery()
            ->includeAuthor()
            ->execute($commentId)
            ->hydrateSingleResultAs(PostWithAuthorDto::class);
    }

    private function getPostQuery(): PostQueryInterface
    {
        return self::getService(PostQueryInterface::class);
    }

    private function findAPost(): Post
    {
        $dqlQuery = $this->dqlQueryBuilder->create(Post::class)->setMaxResults(1)->build();

        return $this->queryService->query($dqlQuery)->getSingleResult();
    }

    private function findUser(UserId $userId): User
    {
        $dqlQuery = $this->dqlQueryBuilder->create(User::class)->where('User.id = :userId')
            ->setParameter('userId', $userId)
            ->build();

        return $this->queryService->query($dqlQuery)->getSingleResult();
    }
}
