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

use Acme\App\Core\Component\Blog\Application\Query\FindHighestPostSlugSuffixQueryInterface;
use Acme\App\Core\Component\Blog\Domain\Entity\Post;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\Exception\EmptyQueryResultException;
use Acme\App\Core\Port\Persistence\QueryServiceInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;

final class FindHighestPostSlugSuffixQuery implements FindHighestPostSlugSuffixQueryInterface
{
    private const SLUG_ALIAS = 'slug';

    /**
     * @var DqlQueryBuilderInterface
     */
    private $dqlQueryBuilder;

    /**
     * @var QueryServiceInterface
     */
    private $queryService;

    public function __construct(
        DqlQueryBuilderInterface $dqlQueryBuilder,
        QueryServiceRouterInterface $queryService
    ) {
        $this->dqlQueryBuilder = $dqlQueryBuilder;
        $this->queryService = $queryService;
    }

    public function execute(string $slug): int
    {
        $this->dqlQueryBuilder->create(Post::class, 'Post')
            ->select('Post.slug as ' . self::SLUG_ALIAS)
            ->where('Post.slug LIKE :slug')
            ->setParameter('slug', $slug . '%');

        $resultCollection = $this->queryService->query($this->dqlQueryBuilder->build());

        if (\count($resultCollection) === 0) {
            throw new EmptyQueryResultException();
        }

        $max = -1;
        foreach ($resultCollection as $result) {
            preg_match("/$slug\-?(\d*)$/", $result[self::SLUG_ALIAS], $matches);
            $max = (isset($matches[1]) && (int) $matches[1] > $max)
                ? (int) $matches[1]
                : $max;
        }

        return $max;
    }
}
