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

use Acme\App\Core\Component\Blog\Application\Query\CommentListQueryInterface;
use Acme\App\Core\Component\Blog\Application\Query\PostQueryInterface;
use Acme\App\Core\Component\Blog\Application\Query\TagListQueryInterface;
use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Core\Port\Persistence\ResultCollection;
use Acme\App\Core\Port\Persistence\ResultCollectionInterface;
use Acme\App\Core\SharedKernel\Component\Blog\Domain\Post\Comment\CommentId;
use Acme\PhpExtension\Exception\AcmeOverloadingException;
use Acme\PhpExtension\Helper\TypeHelper;

/**
 * Some times we have cases where we need very similar query objects, and end up with a set of query objects like:
 *  - FindPostQuery
 *  - FindPostWithAuthorQuery
 *  - FindPostWithAuthorAndTagsQuery
 *  - FindPostWithAuthorAndTagsAndCommentsQuery
 *  - FindPostWithAuthorAndCommentsQuery
 *  - FindPostWithTagsAndCommentsQuery
 *
 * Which is not rally nice.
 *
 * The solution is to create a Query object that we can tell what we actually need to get back each time we use it,
 * in the lines of a builder pattern, using fluent interfaces.
 *
 * This query object is quite flexible and it prevents us from having a set of very similar query objects. Although
 * with this flexibility it comes some added complexity I feel the balance is still positive, in this case.
 *
 * Furthermore, this is good for a generic use where the query doesn't return much data, in this case it returns 30 rows
 * at most (a page in the UI), so we can allow ourselves to get more data than needed in some use cases. However, if
 * it would return more than 500 rows I would prefer to make a specialized query returning only the columns needed for
 * that specific case.
 */
final class PostQuery implements PostQueryInterface
{
    /**
     * @var bool
     */
    private $includeTags = false;

    /**
     * @var bool
     */
    private $includeComments = false;

    /**
     * @var bool
     */
    private $includeAuthor = false;

    /**
     * @var bool
     */
    private $includeCommentsAuthor = false;

    /**
     * @var DqlQueryBuilderInterface
     */
    private $dqlQueryBuilder;

    /**
     * @var QueryServiceRouterInterface
     */
    private $queryService;

    /**
     * @var TagListQueryInterface
     */
    private $tagListQuery;

    /**
     * @var CommentListQueryInterface
     */
    private $commentListQuery;

    public function __construct(
        DqlQueryBuilderInterface $dqlQueryBuilder,
        QueryServiceRouterInterface $queryService,
        TagListQueryInterface $tagListQuery,
        CommentListQueryInterface $commentListQuery
    ) {
        $this->dqlQueryBuilder = $dqlQueryBuilder;
        $this->queryService = $queryService;
        $this->tagListQuery = $tagListQuery;
        $this->commentListQuery = $commentListQuery;
    }

    public function includeTags(): PostQueryInterface
    {
        $this->includeTags = true;

        return $this;
    }

    public function includeComments(): PostQueryInterface
    {
        $this->includeComments = true;

        return $this;
    }

    public function includeCommentsAuthor(): PostQueryInterface
    {
        $this->includeCommentsAuthor = true;

        return $this;
    }

    public function includeAuthor(): PostQueryInterface
    {
        $this->includeAuthor = true;

        return $this;
    }

    /**
     * Since this class only has one public method, it makes sense that it is designed as a callable, using the
     * magic method name `__invoke()`, instead of having a single public method called `execute()` which adds nothing
     * to code readability.
     * However, by using `__invoke()` we lose code completion, so in the end I prefer to use this `execute()` method.
     *
     * This method is also an example of how we can implement method overloading in PHP.
     * Method overloading is the capability of having one class with several methods with the same name, but different
     * parameters.
     * PHP does not offer method overloading because it offers default values for parameters, which makes it very tricky
     * to determine what method should actually be called for a set of arguments (should it use the method with exactly
     * that set of arguments, or the one with that set of arguments plus some other parameter with a default value?).
     * It's a poor mans overloading mechanism, but it's what we can do in PHP.
     *
     * @param PostId|CommentId|string $id
     */
    public function execute($id): ResultCollectionInterface
    {
        $type = TypeHelper::getType($id);
        switch ($type) {
            case PostId::class:
                return $this->findUsingPostId($id);
            case CommentId::class:
                return $this->findUsingCommentId($id);
            case 'string':
                return $this->findUsingSlug($id);
            default:
                throw new AcmeOverloadingException(
                    'Can handle arguments of types ' . PostId::class . ' and ' . CommentId::class . '.'
                    . " Argument of type '$type' provided."
                );
        }
    }

    private function findUsingPostId(PostId $postId): ResultCollectionInterface
    {
        return $this->getPostData(function (DqlQueryBuilderInterface $queryBuilder) use ($postId): void {
            $queryBuilder->where('Post.id = :postId')
                ->setParameter('postId', $postId);
        });
    }

    private function findUsingCommentId(CommentId $commentId): ResultCollectionInterface
    {
        return $this->getPostData(
            function (DqlQueryBuilderInterface $queryBuilder) use ($commentId): void {
                $queryBuilder->join('Post.comments', 'Comments')
                    ->where('Comments = :commentId')
                    ->setParameter('commentId', $commentId);
            }
        );
    }

    private function findUsingSlug(string $slug): ResultCollectionInterface
    {
        return $this->getPostData(
            function (DqlQueryBuilderInterface $queryBuilder) use ($slug): void {
                $queryBuilder->where('Post.slug = :slug')
                    ->setParameter('slug', $slug);
            }
        );
    }

    private function getPostData(callable $matchingFunction): ResultCollectionInterface
    {
        $this->dqlQueryBuilder->create(Post::class, 'Post')
            ->select(
                'Post.id',
                'Post.title',
                'Post.publishedAt',
                'Post.summary',
                'Post.content',
                'Post.slug'
            );

        $matchingFunction($this->dqlQueryBuilder);

        if ($this->includeAuthor) {
            $this->joinAuthor($this->dqlQueryBuilder);
        }

        $postData = $this->queryService->query($this->dqlQueryBuilder->build())
            ->getSingleResult();

        if (empty($postData)) {
            return new ResultCollection();
        }

        if ($this->includeTags) {
            $postData['tagList'] = $this->findTags($postData['id']);
        }

        if ($this->includeComments) {
            $postData['commentList'] = $this->findComments($postData['id']);
        }

        $this->resetJoins();

        return new ResultCollection([$postData]);
    }

    private function joinAuthor(DqlQueryBuilderInterface $queryBuilder): void
    {
        $queryBuilder->addSelect(
            'Author.id AS authorId',
            'Author.mobile AS authorMobile',
            'Author.fullName AS authorFullName',
            'Author.email AS authorEmail'
        )
            // This join with 'User:User' is the same as a join with User::class. The main difference is that this way
            // we are not depending directly on the User entity, but on a configurable alias. The advantage is that we
            // can change where the user data is stored and this query will remain the same. For example we could move
            // this component into a microservice, with its own curated user data, and we wouldn't need to change this
            // query, only the doctrine configuration.
            ->join('User:User', 'Author', 'WITH', 'Author.id = Post.authorId');
    }

    private function findTags(PostId $postId): array
    {
        return $this->tagListQuery->execute($postId)->toArray();
    }

    private function findComments(PostId $postId): array
    {
        if ($this->includeCommentsAuthor) {
            $this->commentListQuery->includeAuthor();
        }

        return $this->commentListQuery->execute($postId)->toArray();
    }

    private function resetJoins(): void
    {
        $this->includeTags = false;
        $this->includeAuthor = false;
        $this->includeComments = false;
        $this->includeCommentsAuthor = false;
    }
}
