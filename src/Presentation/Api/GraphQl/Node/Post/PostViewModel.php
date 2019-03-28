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

namespace Acme\App\Presentation\Api\GraphQl\Node\Post;

use Acme\App\Core\Component\Blog\Domain\Post\Post;
use DateTimeInterface;

final class PostViewModel
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $summary;

    /**
     * @var string
     */
    private $content;

    /**
     * @var DateTimeInterface
     */
    private $publishedAt;

    private function __construct(
        string $id,
        string $title,
        string $slug,
        string $summary,
        string $content,
        DateTimeInterface $publishedAt
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->summary = $summary;
        $this->content = $content;
        $this->publishedAt = $publishedAt;
    }

    public static function constructFromEntity(Post $post): self
    {
        return new self(
            $post->getId()->toScalar(),
            $post->getTitle(),
            $post->getSlug(),
            $post->getSummary(),
            $post->getContent(),
            $post->getPublishedAt()
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getPublishedAt(): DateTimeInterface
    {
        return $this->publishedAt;
    }
}
