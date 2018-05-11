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

namespace Acme\App\Presentation\Web\Core\Component\Blog\Admin\PostList;

use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Port\TemplateEngine\TemplateViewModelInterface;
use DateTimeInterface;

final class GetViewModel implements TemplateViewModelInterface
{
    /**
     * @var array [PostId, string, DateTimeInterface]
     */
    private $postList = [];

    /**
     * The view model constructor depends on the most raw elements possible.
     * In this case the view model constructor is private, so that we force the usage of the named constructor.
     * We want this because it is not possible to have the a constructor with the raw data, since it is a list of data.
     */
    private function __construct()
    {
    }

    /**
     * We create named constructors for the cases where we need to extract the raw data from complex data structures.
     */
    public static function fromPostList(Post ...$postList): self
    {
        $viewModel = new self();
        foreach ($postList as $post) {
            $viewModel->addPostData($post->getId(), $post->getTitle(), $post->getPublishedAt());
        }

        return $viewModel;
    }

    private function addPostData(
        PostId $id,
        string $title,
        DateTimeInterface $publishedAt
    ): void {
        $this->postList[] = [
            'id' => $id,
            'title' => $title,
            'publishedAt' => $publishedAt,
        ];
    }

    public function getPostList(): array
    {
        return $this->postList;
    }
}
