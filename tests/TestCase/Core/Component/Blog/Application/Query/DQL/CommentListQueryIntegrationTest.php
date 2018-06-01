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

use Acme\App\Core\Component\Blog\Application\Query\CommentListQueryInterface;
use Acme\App\Core\Component\Blog\Domain\Post\Comment\Comment;
use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Test\Framework\AbstractIntegrationTest;

/**
 * @medium
 */
final class CommentListQueryIntegrationTest extends AbstractIntegrationTest
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
    public function execute(): void
    {
        $post = $this->findAPost();
        $postId = $post->getId();
        $this->clearDatabaseCache();

        $postCommentList = array_map(
            function (Comment $comment) {
                return (string) $comment->getId();
            },
            $post->getComments()->toArray()
        );
        sort($postCommentList);

        $queryCommentList = array_map(
            function (array $comment) {
                return (string) $comment[CommentListQueryInterface::KEY_ID];
            },
            $this->getCommentListQuery()->execute($postId)->toArray()
        );
        sort($queryCommentList);

        self::assertSame($postCommentList, $queryCommentList);
    }

    private function getCommentListQuery(): CommentListQueryInterface
    {
        return self::getService(CommentListQueryInterface::class);
    }

    private function findAPost(): Post
    {
        $dqlQuery = $this->dqlQueryBuilder->create(Post::class)->setMaxResults(1)->build();

        return $this->queryService->query($dqlQuery)->getSingleResult();
    }
}
