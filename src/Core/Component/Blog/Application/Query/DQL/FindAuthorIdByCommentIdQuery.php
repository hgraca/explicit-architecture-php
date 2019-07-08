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

use Acme\App\Core\Component\Blog\Application\Query\FindAuthorIdByCommentIdQueryInterface;
use Acme\App\Core\Component\Blog\Domain\Post\Comment\Comment;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Core\SharedKernel\Component\Blog\Domain\Post\Comment\CommentId;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;

final class FindAuthorIdByCommentIdQuery implements FindAuthorIdByCommentIdQueryInterface
{
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

    public function execute(CommentId $commentId): UserId
    {
        $dqlQuery = $this->dqlQueryBuilder->create(Comment::class)
            ->select('Comment.authorId')
            ->where('Comment.id = :commentId')
            ->setParameter('commentId', $commentId)
            ->build();

        return $this->queryService->query($dqlQuery)->getSingleResult()['authorId'];
    }
}
