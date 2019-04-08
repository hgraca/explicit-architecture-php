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

namespace Acme\App\Core\Component\Blog\Application\Repository\DQL;

use Acme\App\Core\Component\Blog\Application\Repository\TagRepositoryInterface;
use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Component\Blog\Domain\Post\Tag\Tag;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Core\Port\Persistence\ResultCollection;
use Acme\App\Core\Port\Persistence\ResultCollectionInterface;

class TagRepository implements TagRepositoryInterface
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

    /**
     * @return Tag[]
     */
    public function findAllByPostId(
        PostId $postId,
        array $orderByList = ['name' => 'DESC'],
        int $maxResults = null
    ): ResultCollectionInterface {
        $this->dqlQueryBuilder->create(Post::class);

        $this->dqlQueryBuilder->addSelect('Tag')
            ->innerJoin('Post.tags', 'Tag')
            ->where('Post.id = :post')
            ->setParameter('post', $postId);

        foreach ($orderByList as $property => $direction) {
            $this->dqlQueryBuilder->orderBy('Tag.' . $property, $direction);
        }

        if ($maxResults) {
            $this->dqlQueryBuilder->setMaxResults($maxResults);
        }

        /** @var Post $post */
        $post = $this->queryService->query($this->dqlQueryBuilder->build())->getSingleResult();

        return new ResultCollection($post->getTags());
    }
}
