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

namespace Acme\App\Presentation\Web\Core\Component\Blog\Anonymous\Post;

use Acme\App\Core\Component\Blog\Domain\Entity\Comment;
use Acme\App\Core\Component\Blog\Domain\Entity\Post;
use Acme\App\Core\Component\Blog\Domain\Entity\PostId;
use Acme\App\Core\Port\TemplateEngine\TemplateViewModelInterface;
use DateTimeInterface;

final class GetViewModel implements TemplateViewModelInterface
{
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
    private $postContent;

    /**
     * @var string[]
     */
    private $postTagList;

    /**
     * @var string[][]
     */
    private $postCommentList;

    /**
     * The view model constructor depends on the most raw elements possible.
     */
    public function __construct(
        PostId $id,
        string $title,
        DateTimeInterface $publishedAt,
        string $authorFullName,
        string $content,
        array $postTagList,
        array $postCommentList
    ) {
        $this->postId = $id;
        $this->postTitle = $title;
        $this->postPublishedAt = $publishedAt;
        $this->postAuthorFullName = $authorFullName;
        $this->postContent = $content;
        $this->postTagList = $postTagList;
        $this->postCommentList = $postCommentList;
    }

    /**
     * We create named constructors for the cases where we need to extract the raw data from complex data structures.
     */
    public static function fromPostAndCommentList(Post $post, Comment ...$commentList): self
    {
        $postTagList = [];
        foreach ($post->getTags() as $tag) {
            $postTagList[] = (string) $tag;
        }

        $postCommentList = [];
        foreach ($commentList as $comment) {
            $postCommentList[] = [
                'id' => $comment->getId(),
                'publishedAt' => $comment->getPublishedAt(),
                'content' => $comment->getContent(),
                'authorFullName' => $comment->getAuthor()->getFullName(),
            ];
        }

        return new self(
            $post->getId(),
            $post->getTitle(),
            $post->getPublishedAt(),
            $post->getAuthor()->getFullName(),
            $post->getContent(),
            $postTagList,
            $postCommentList
        );
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

    public function getPostContent(): string
    {
        return $this->postContent;
    }

    /**
     * @return string[]
     */
    public function getPostCommentList(): array
    {
        return $this->postCommentList;
    }

    public function getPostTagList(): array
    {
        return $this->postTagList;
    }
}
