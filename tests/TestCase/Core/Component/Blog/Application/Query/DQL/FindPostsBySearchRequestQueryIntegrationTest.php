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

use Acme\App\Core\Component\Blog\Application\Query\DQL\FindPostsBySearchRequestQuery;
use Acme\App\Core\Component\Blog\Application\Query\FindPostsBySearchRequestQueryInterface;
use Acme\App\Core\Port\Persistence\QueryServiceInterface;
use Acme\App\Infrastructure\Persistence\Doctrine\DqlPersistenceService;
use Acme\App\Test\Framework\AbstractIntegrationTest;

final class FindPostsBySearchRequestQueryIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @var FindPostsBySearchRequestQueryInterface
     */
    private $query;

    /**
     * @var DqlPersistenceService
     */
    private $persistenceService;

    public function setUp(): void
    {
        $this->query = self::getService(FindPostsBySearchRequestQuery::class);
        $this->persistenceService = self::getService(QueryServiceInterface::class);
    }

    /**
     * @test
     */
    public function findBySearchQuery(): void
    {
        $rawQuery = 'lo';
        $postList = $this->query->execute($rawQuery);

        self::assertGreaterThan(0, $postList->count());

        foreach ($postList as $post) {
            self::assertContains($rawQuery, $post->getTitle());
        }
    }
}
