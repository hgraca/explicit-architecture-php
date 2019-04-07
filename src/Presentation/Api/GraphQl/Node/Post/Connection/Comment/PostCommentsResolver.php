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

namespace Acme\App\Presentation\Api\GraphQl\Node\Post\Connection\Comment;

use Acme\App\Core\Component\Blog\Application\Repository\CommentRepositoryInterface;
use Acme\App\Core\Component\Blog\Domain\Post\Comment\Comment;
use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Port\Persistence\Exception\EmptyQueryResultException;
use Acme\App\Presentation\Api\GraphQl\Node\Comment\CommentViewModel;
use Doctrine\Common\Collections\Collection;
use Overblog\GraphQLBundle\Relay\Connection\Output\Connection;
use Overblog\GraphQLBundle\Relay\Connection\Output\ConnectionBuilder;
use function array_map;
use function count;

final class PostCommentsResolver
{
    /**
     * @var CommentRepositoryInterface
     */
    private $commentRepository;

    public function __construct(
        CommentRepositoryInterface $commentRepository
    ) {
        $this->commentRepository = $commentRepository;
    }

    public function getPostCommentsConnection(PostId $postId): Connection
    {
        try {
            /** @var Collection $postCommentList */
            $postCommentList = $this->commentRepository->findAllByPostId($postId);
        } catch (EmptyQueryResultException $e) {
            return ConnectionBuilder::connectionFromArray([]);
        }

        $commentViewModelList = array_map(
            function (Comment $comment) {
                return CommentViewModel::constructFromEntity($comment);
            },
            $postCommentList->toArray()
        );

        return ConnectionBuilder::connectionFromArray($commentViewModelList);
    }

    public function countEdges(Connection $connection): int
    {
        return count($connection->edges);
    }
}
