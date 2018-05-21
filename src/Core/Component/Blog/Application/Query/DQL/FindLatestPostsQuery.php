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

use Acme\App\Core\Component\Blog\Application\Query\FindLatestPostsQueryInterface;
use Acme\App\Core\Component\Blog\Application\Query\LatestPostWithAuthorAndTagsDto;
use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Component\Blog\Domain\Post\Tag\Tag;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryServiceInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Core\Port\Persistence\ResultCollection;
use Acme\App\Core\Port\Persistence\ResultCollectionInterface;
use Acme\PhpExtension\DateTime\DateTimeGenerator;

final class FindLatestPostsQuery implements FindLatestPostsQueryInterface
{
    private const KEY_POST_ID = 'post_id';
    private const KEY_TAG = 'tag';

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
     * Since this class only has one public method, it makes sense that it is designed as a callable, using the
     * magic method name `__invoke()`, instead of having a single public method called `execute()` which adds nothing
     * to code readability.
     * However, by using `__invoke()` we lose code completion, so in the end I prefer to use this `execute()` method.
     *
     * @return LatestPostWithAuthorAndTagsDto[]
     */
    public function execute(int $limit = self::NUM_ITEMS): ResultCollectionInterface
    {
        $postListResult = $this->getPostList($limit);
        $normalizedPostIdList = $this->extractNormalizedPostIdList($postListResult);

        $tagListResult = $this->getRelatedTagList(...$normalizedPostIdList);
        $tagListPerPostId = $this->groupTagsPerPostId($tagListResult);

        $postListWithTagList = $this->addTagListToPostList($postListResult->toArray(), $tagListPerPostId);
        $result = new ResultCollection($postListWithTagList);

        return $result->hydrateResultItemsAs(LatestPostWithAuthorAndTagsDto::class);
    }

    private function getPostList(int $limit): ResultCollectionInterface
    {
        $dqlQuery = $this->dqlQueryBuilder->create(Post::class, 'Post')
            ->select(
                'Post.id',
                'Post.title',
                'Post.publishedAt',
                'Post.summary',
                'Post.slug',
                'Author.email AS authorEmail',
                'Author.fullName AS authorFullName'
            )
            // This join with 'User:User' is the same as a join with User::class. The main difference is that this way
            // we are not depending directly on the User entity, but on a configurable alias. The advantage is that we
            // can change where the user data is stored and this query will remain the same. For example we could move
            // this component into a microservice, with its own curated user data, and we wouldn't need to change this
            // query, only the doctrine configuration.
            ->join('User:User', 'Author', 'WITH', 'Author.id = Post.authorId')
            ->where('Post.publishedAt <= :now')
            ->orderBy('Post.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->setParameter('now', DateTimeGenerator::generate())
            ->build();

        return $this->queryService->query($dqlQuery);
    }

    private function extractNormalizedPostIdList(ResultCollectionInterface $resultCollection): array
    {
        $postIdList = [];
        foreach ($resultCollection as $postData) {
            $postIdList[] = (string) $postData['id'];
        }

        return $postIdList;
    }

    private function getRelatedTagList(string ...$normalizedPostIdList): ResultCollectionInterface
    {
        $dqlQuery = $this->dqlQueryBuilder->create(Tag::class, 'Tag')
            ->select('Post.id AS ' . self::KEY_POST_ID, 'Tag.name AS ' . self::KEY_TAG)
            ->join(Post::class, 'Post')
            ->where("Post.id IN ('" . implode("', '", $normalizedPostIdList) . "')")
            ->useScalarHydration()
            ->build();

        return $this->queryService->query($dqlQuery);
    }

    private function groupTagsPerPostId(ResultCollectionInterface $resultCollection): array
    {
        $tagList = [];
        foreach ($resultCollection as $tagData) {
            $tagList[$tagData[self::KEY_POST_ID]][] = $tagData[self::KEY_TAG];
        }

        return $tagList;
    }

    private function addTagListToPostList(array $postListResult, array $tagListPerPostId): array
    {
        foreach ($postListResult as &$postData) {
            $postData['tagList'] = isset($tagListPerPostId[(string) $postData['id']])
                ? $tagListPerPostId[(string) $postData['id']]
                : [];
        }

        return $postListResult;
    }
}
