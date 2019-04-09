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

namespace Acme\App\Presentation\Api\GraphQl\Node\Post\Connection\Author;

use Acme\App\Core\Component\Blog\Application\Query\FindAuthorIdByPostIdQueryInterface;
use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Component\User\Application\Repository\UserRepositoryInterface;
use Acme\App\Core\Port\Persistence\Exception\EmptyQueryResultException;
use Acme\App\Presentation\Api\GraphQl\Node\User\AbstractUserViewModel;
use Overblog\GraphQLBundle\Relay\Connection\Output\Connection;
use Overblog\GraphQLBundle\Relay\Connection\Output\ConnectionBuilder;
use function count;

final class PostAuthorsResolver
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var FindAuthorIdByPostIdQueryInterface
     */
    private $findAuthorIdByPostIdQuery;

    public function __construct(
        UserRepositoryInterface $userRepository,
        FindAuthorIdByPostIdQueryInterface $findAuthorIdByPostIdQuery
    ) {
        $this->userRepository = $userRepository;
        $this->findAuthorIdByPostIdQuery = $findAuthorIdByPostIdQuery;
    }

    public function getPostAuthorsConnection(PostId $postId): Connection
    {
        try {
            $author = $this->userRepository->findOneById(
                $this->findAuthorIdByPostIdQuery->execute($postId)
            );
        } catch (EmptyQueryResultException $e) {
            return ConnectionBuilder::connectionFromArray([]);
        }

        return ConnectionBuilder::connectionFromArray(
            [
                AbstractUserViewModel::constructFromEntity($author),
            ]
        );
    }

    public function countEdges(Connection $connection): int
    {
        return count($connection->edges);
    }
}
