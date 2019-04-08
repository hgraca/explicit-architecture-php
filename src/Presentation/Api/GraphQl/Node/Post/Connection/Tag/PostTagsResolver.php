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

namespace Acme\App\Presentation\Api\GraphQl\Node\Post\Connection\Tag;

use Acme\App\Core\Component\Blog\Application\Repository\TagRepositoryInterface;
use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Component\Blog\Domain\Post\Tag\Tag;
use Acme\App\Core\Port\Persistence\Exception\EmptyQueryResultException;
use Acme\App\Presentation\Api\GraphQl\Node\Tag\TagViewModel;
use Doctrine\Common\Collections\Collection;
use Overblog\GraphQLBundle\Relay\Connection\Output\Connection;
use Overblog\GraphQLBundle\Relay\Connection\Output\ConnectionBuilder;
use function array_map;
use function count;

final class PostTagsResolver
{
    /**
     * @var TagRepositoryInterface
     */
    private $tagRepository;

    public function __construct(
        TagRepositoryInterface $tagRepository
    ) {
        $this->tagRepository = $tagRepository;
    }

    public function getPostTagsConnection(PostId $postId): Connection
    {
        try {
            /** @var Collection $postTagList */
            $postTagList = $this->tagRepository->findAllByPostId($postId);
        } catch (EmptyQueryResultException $e) {
            return ConnectionBuilder::connectionFromArray([]);
        }

        $tagViewModelList = array_map(
            function (Tag $tag) {
                return TagViewModel::constructFromEntity($tag);
            },
            $postTagList->toArray()
        );

        return ConnectionBuilder::connectionFromArray($tagViewModelList);
    }

    public function countEdges(Connection $connection): int
    {
        return count($connection->edges);
    }
}
