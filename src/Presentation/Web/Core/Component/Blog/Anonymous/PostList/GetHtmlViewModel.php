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

namespace Acme\App\Presentation\Web\Core\Component\Blog\Anonymous\PostList;

use Acme\App\Core\Component\Blog\Domain\Entity\Post;
use Acme\App\Core\Port\TemplateEngine\TemplateViewModelInterface;
use Acme\App\Presentation\Web\Core\Port\Paginator\PaginatorFactoryInterface;
use Acme\App\Presentation\Web\Core\Port\Paginator\PaginatorInterface;

final class GetHtmlViewModel implements TemplateViewModelInterface
{
    /**
     * @var PaginatorInterface [string, string, string, DateTimeInterface, string, string[]]
     */
    private $postList;

    /**
     * The view model constructor depends on the most raw elements possible.
     * In this case the view model constructor is private, so that we force the usage of the named constructor.
     * We want this because it is not possible to have the a constructor with the raw data, since it is a list of data.
     */
    private function __construct(PaginatorInterface $postList)
    {
        $this->postList = $postList;
    }

    /**
     * We create named constructors for the cases where we need to extract the raw data from complex data structures.
     */
    public static function fromPostList(PaginatorFactoryInterface $paginatorFactory, int $page, Post ...$postList): self
    {
        $postDataList = [];
        foreach ($postList as $post) {
            $postTagList = [];
            foreach ($post->getTags() as $tag) {
                $postTagList[] = (string) $tag;
            }

            $postDataList[] = [
                'title' => $post->getTitle(),
                'summary' => $post->getSummary(),
                'slug' => $post->getSlug(),
                'publishedAt' => $post->getPublishedAt(),
                'authorFullName' => $post->getAuthor()->getFullName(),
                'tagList' => $postTagList,
            ];
        }

        $paginator = $paginatorFactory->createPaginator($postDataList);
        $paginator->setCurrentPage($page);

        return new self($paginator);
    }

    public function getPostList(): PaginatorInterface
    {
        return $this->postList;
    }
}
