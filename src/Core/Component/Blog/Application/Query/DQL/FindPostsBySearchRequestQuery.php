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

use Acme\App\Core\Component\Blog\Application\Query\FindPostsBySearchRequestQueryInterface;
use Acme\App\Core\Component\Blog\Application\Query\PostsBySearchRequestDto;
use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryServiceInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Core\Port\Persistence\ResultCollection;
use Acme\App\Core\Port\Persistence\ResultCollectionInterface;

final class FindPostsBySearchRequestQuery implements FindPostsBySearchRequestQueryInterface
{
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

    /**
     * @throws \Exception
     *
     * @return PostsBySearchRequestDto[]
     */
    public function execute(string $queryString, int $limit = self::NUM_ITEMS): ResultCollectionInterface
    {
        $queryString = $this->sanitizeSearchQuery($queryString);
        $searchTerms = $this->extractSearchTerms($queryString);

        if (\count($searchTerms) === 0) {
            return new ResultCollection();
        }

        $this->dqlQueryBuilder->create(Post::class, 'Post')
            ->select(
                'Post.title',
                'Post.publishedAt',
                'Post.summary',
                'Post.slug',
                'Author.fullName'
            )
            // This join with 'User:User' is the same as a join with User::class. The main difference is that this way
            // we are not depending directly on the User entity, but on a configurable alias. The advantage is that we
            // can change where the user data is stored and this query will remain the same. For example we could move
            // this component into a microservice, with its own curated user data, and we wouldn't need to change this
            // query, only the doctrine configuration.
            ->join('User:User', 'Author', 'WITH', 'Author.id = Post.authorId');

        foreach ($searchTerms as $key => $term) {
            $this->dqlQueryBuilder->orWhere('Post.title LIKE :t_' . $key)
                ->setParameter('t_' . $key, '%' . $term . '%');
        }

        $this->dqlQueryBuilder->orderBy('Post.publishedAt', 'DESC')->setMaxResults($limit);

        $result = $this->queryService
            ->query($this->dqlQueryBuilder->build());

        return $result->hydrateResultItemsAs(PostsBySearchRequestDto::class);
    }

    /**
     * Removes all non-alphanumeric characters except whitespaces.
     */
    private function sanitizeSearchQuery(string $query): string
    {
        return trim(preg_replace('/[[:space:]]+/', ' ', $query));
    }

    /**
     * Splits the search query into terms and removes the ones which are irrelevant.
     */
    private function extractSearchTerms(string $searchQuery): array
    {
        $terms = array_unique(explode(' ', $searchQuery));

        return array_filter(
            $terms,
            function ($term) {
                return 2 <= mb_strlen($term);
            }
        );
    }
}
