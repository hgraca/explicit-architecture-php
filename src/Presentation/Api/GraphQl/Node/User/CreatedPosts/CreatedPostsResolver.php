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

namespace Acme\App\Presentation\Api\GraphQl\Node\User\CreatedPosts;

use Acme\App\Core\Component\Blog\Application\Repository\PostRepositoryInterface;
use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;
use Acme\App\Presentation\Api\GraphQl\Node\Post\PostViewModel;
use Overblog\GraphQLBundle\Relay\Connection\Output\Connection;
use Overblog\GraphQLBundle\Relay\Connection\Output\ConnectionBuilder;
use function array_map;

final class CreatedPostsResolver
{
    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    public function __construct(
        PostRepositoryInterface $postRepository
    ) {
        $this->postRepository = $postRepository;
    }

    public function getCreatedPostsConnection(UserId $userId): Connection
    {
        $postViewModelList = array_map(
            function (Post $post) {
                return PostViewModel::constructFromEntity($post);
            },
            $this->postRepository->findAllByUserId($userId)->toArray()
        );

        return ConnectionBuilder::connectionFromArray($postViewModelList);
    }

    public function countEdges(Connection $connection): int
    {
        return count($connection->edges);
    }
}
