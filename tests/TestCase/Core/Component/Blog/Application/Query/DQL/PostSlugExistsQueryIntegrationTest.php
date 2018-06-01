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

use Acme\App\Core\Component\Blog\Application\Query\DQL\PostSlugExistsQuery;
use Acme\App\Core\Component\Blog\Application\Query\PostSlugExistsQueryInterface;
use Acme\App\Core\Component\Blog\Application\Repository\DQL\PostRepository;
use Acme\App\Core\Component\Blog\Application\Repository\PostRepositoryInterface;
use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Core\Port\Persistence\TransactionServiceInterface;
use Acme\App\Test\Framework\AbstractIntegrationTest;

/**
 * @medium
 */
final class PostSlugExistsQueryIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @test
     */
    public function execute_returns_true_if_several_slugs_exist(): void
    {
        $titleStem = 'Some Interesting Title';
        $maxSuffix = 21;
        $author = $this->getAnAuthor();

        $this->getTransactionService()->startTransaction();

        $post1 = new Post();
        $post1->setAuthorId($author->getId());
        $post1->setTitle($titleStem);
        $post1->setSummary('some summary ...');
        $post1->setContent('some content ...');
        $this->getPostRepository()->add($post1);

        $post2 = new Post();
        $post2->setAuthorId($author->getId());
        $post2->setTitle($titleStem);
        $post2->setSummary('some summary ...');
        $post2->setContent('some content ...');
        $post2->postfixSlug((string) $maxSuffix);
        $this->getPostRepository()->add($post2);

        $post3 = new Post();
        $post3->setAuthorId($author->getId());
        $post3->setTitle($titleStem);
        $post3->setSummary('some summary ...');
        $post3->setContent('some content ...');
        $post3->postfixSlug((string) ($maxSuffix - 3));
        $this->getPostRepository()->add($post3);

        $this->getTransactionService()->finishTransaction();

        self::assertTrue($this->getPostSlugExistsQuery()->execute($post1->getSlug()));
    }

    /**
     * @test
     */
    public function execute_returns_true_if_only_post_fixed_slug_found(): void
    {
        $titleStem = 'Some Interesting Title';
        $maxSuffix = 21;
        $author = $this->getAnAuthor();

        $this->getTransactionService()->startTransaction();

        $post1 = new Post();
        $post1->setAuthorId($author->getId());
        $post1->setTitle($titleStem);
        $post1->setSummary('some summary ...');
        $post1->setContent('some content ...');
        $post1->postfixSlug((string) $maxSuffix);
        $this->getPostRepository()->add($post1);

        $this->getTransactionService()->finishTransaction();

        self::assertTrue($this->getPostSlugExistsQuery()->execute($post1->getSlug()));
    }

    /**
     * @test
     */
    public function execute_returns_true_if_only_stem_slug_found(): void
    {
        $titleStem = 'Some Interesting Title';
        $author = $this->getAnAuthor();

        $this->getTransactionService()->startTransaction();

        $post = new Post();
        $post->setAuthorId($author->getId());
        $post->setTitle($titleStem);
        $post->setSummary('some summary ...');
        $post->setContent('some content ...');
        $this->getPostRepository()->add($post);

        $this->getTransactionService()->finishTransaction();

        self::assertTrue($this->getPostSlugExistsQuery()->execute($post->getSlug()));
    }

    /**
     * @test
     */
    public function execute_returns_false_if_slug_not_found(): void
    {
        $titleStem = 'Some Interesting Title';
        $post = new Post();
        $post->setTitle($titleStem);

        self::assertFalse($this->getPostSlugExistsQuery()->execute($post->getSlug()));
    }

    private function getPostRepository(): PostRepositoryInterface
    {
        return self::getService(PostRepository::class);
    }

    private function getDqlQueryBuilder(): DqlQueryBuilderInterface
    {
        return self::getService(DqlQueryBuilderInterface::class);
    }

    private function getTransactionService(): TransactionServiceInterface
    {
        return self::getService(TransactionServiceInterface::class);
    }

    private function getQueryService(): QueryServiceRouterInterface
    {
        return self::getService(QueryServiceRouterInterface::class);
    }

    private function getAnAuthor(): User
    {
        $dqlQuery = $this->getDqlQueryBuilder()->create(User::class)->setMaxResults(1)->build();

        return $this->getQueryService()->query($dqlQuery)->getSingleResult();
    }

    private function getPostSlugExistsQuery(): PostSlugExistsQueryInterface
    {
        return self::getService(PostSlugExistsQuery::class);
    }
}
