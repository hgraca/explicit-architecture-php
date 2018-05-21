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

namespace Acme\App\Presentation\Web\Core\Component\Blog\Admin\Post;

use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Port\TemplateEngine\TemplateViewModelInterface;
use Acme\PhpExtension\ConstructableFromArrayInterface;
use Acme\PhpExtension\ConstructableFromArrayTrait;
use DateTimeInterface;

final class GetViewModel implements TemplateViewModelInterface, ConstructableFromArrayInterface
{
    use ConstructableFromArrayTrait;

    /**
     * @var PostId
     */
    private $postId;

    /**
     * @var string
     */
    private $postTitle;

    /**
     * @var DateTimeInterface
     */
    private $postPublishedAt;

    /**
     * @var string
     */
    private $postAuthorFullName;

    /**
     * @var string
     */
    private $postSummary;

    /**
     * @var string
     */
    private $postContent;

    /**
     * @var string[]
     */
    private $postTagList;

    /**
     * The view model constructor depends on the most raw elements possible.
     */
    public function __construct(
        PostId $id,
        string $title,
        DateTimeInterface $publishedAt,
        string $authorFullName,
        string $summary,
        string $content,
        string ...$tagList
    ) {
        $this->postId = $id;
        $this->postTitle = $title;
        $this->postPublishedAt = $publishedAt;
        $this->postAuthorFullName = $authorFullName;
        $this->postSummary = $summary;
        $this->postContent = $content;
        $this->postTagList = $tagList;
    }

    public function getPostId(): PostId
    {
        return $this->postId;
    }

    public function getPostTitle(): string
    {
        return $this->postTitle;
    }

    public function getPostPublishedAt(): DateTimeInterface
    {
        return $this->postPublishedAt;
    }

    public function getPostAuthorFullName(): string
    {
        return $this->postAuthorFullName;
    }

    public function getPostSummary(): string
    {
        return $this->postSummary;
    }

    public function getPostContent(): string
    {
        return $this->postContent;
    }

    /**
     * @return string[]
     */
    public function getPostTagList(): array
    {
        return $this->postTagList;
    }
}
