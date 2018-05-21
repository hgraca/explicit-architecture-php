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

use Acme\App\Core\Component\Blog\Application\Query\TagListQueryInterface;
use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Core\Port\Persistence\ResultCollection;
use Acme\App\Core\Port\Persistence\ResultCollectionInterface;

final class TagListQuery implements TagListQueryInterface
{
    private const KEY_TAG = 'tag';

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

    public function execute(PostId $postId): ResultCollectionInterface
    {
        $dqlQuery = $this->dqlQueryBuilder->create(Post::class, 'Post')
            ->select('Tag.name AS ' . self::KEY_TAG)
            ->join('Post.tags', 'Tag')
            ->where('Post.id = :postId')
            ->setParameter('postId', $postId)
            ->useScalarHydration()
            ->build();

        $result = $this->queryService->query($dqlQuery)->toArray();

        return new ResultCollection($this->flattenTagList($result));
    }

    private function flattenTagList(array $result): array
    {
        return array_map(
            function (array $a) {
                return $a[self::KEY_TAG];
            },
            $result
        );
    }
}
