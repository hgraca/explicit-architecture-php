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

use Acme\App\Core\Component\Blog\Application\Query\TagListQueryInterface;
use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Component\Blog\Domain\Post\Tag\Tag;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Test\Framework\AbstractIntegrationTest;

/**
 * @medium
 */
final class TagListQueryIntegrationTest extends AbstractIntegrationTest
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

        $postTagList = array_map(
            function (Tag $tag) {
                return (string) $tag;
            },
            $post->getTags()
        );
        sort($postTagList);

        $queryTagList = $this->getTagListQuery()->execute($postId)->toArray();
        sort($queryTagList);

        self::assertSame($postTagList, $queryTagList);
    }

    private function getTagListQuery(): TagListQueryInterface
    {
        return self::getService(TagListQueryInterface::class);
    }

    private function findAPost(): Post
    {
        $dqlQuery = $this->dqlQueryBuilder->create(Post::class)->setMaxResults(1)->build();

        return $this->queryService->query($dqlQuery)->getSingleResult();
    }
}
