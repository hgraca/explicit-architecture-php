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

namespace Acme\App\Core\Component\Blog\Application\Query\DQL;

use Acme\App\Core\Component\Blog\Application\Query\CommentListQueryInterface;
use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Core\Port\Persistence\ResultCollectionInterface;

final class CommentListQuery implements CommentListQueryInterface
{
    /**
     * @var bool
     */
    private $includeAuthor = false;

    /**
     * @var DqlQueryBuilderInterface
     */
    private $dqlQueryBuilder;

    /**
     * @var QueryServiceRouterInterface
     */
    private $queryService;

    public function __construct(
        DqlQueryBuilderInterface $dqlQueryBuilder,
        QueryServiceRouterInterface $queryService
    ) {
        $this->dqlQueryBuilder = $dqlQueryBuilder;
        $this->queryService = $queryService;
    }

    public function includeAuthor(): CommentListQueryInterface
    {
        $this->includeAuthor = true;

        return $this;
    }

    public function execute(PostId $postId): ResultCollectionInterface
    {
        $this->dqlQueryBuilder->create(Post::class, 'Post')
            ->select(
                'Comment.id AS ' . self::KEY_ID,
                'Comment.publishedAt AS publishedAt',
                'Comment.content AS content'
            )
            ->join('Post.comments', 'Comment')
            ->where('Post.id = :postId')
            ->setParameter('postId', $postId)
            ->orderBy('publishedAt', 'DESC');

        if ($this->includeAuthor) {
            $this->joinAuthor($this->dqlQueryBuilder);
        }

        $this->resetJoins();

        return $this->queryService->query($this->dqlQueryBuilder->build());
    }

    private function joinAuthor(DqlQueryBuilderInterface $queryBuilder): void
    {
        $queryBuilder->addSelect('Author.fullName AS authorFullName')
            // This join with 'User:User' is the same as a join with User::class. The main difference is that this way
            // we are not depending directly on the User entity, but on a configurable alias. The advantage is that we
            // can change where the user data is stored and this query will remain the same. For example we could move
            // this component into a microservice, with its own curated user data, and we wouldn't need to change this
            // query, only the doctrine alias configuration.
            ->join('User:User', 'Author', 'WITH', 'Author.id = Comment.authorId');
    }

    private function resetJoins(): void
    {
        $this->includeAuthor = false;
    }
}
