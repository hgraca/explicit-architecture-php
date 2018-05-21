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

namespace Acme\App\Core\Component\Blog\Application\Query;

use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\PhpExtension\ConstructableFromArrayInterface;
use Acme\PhpExtension\ConstructableFromArrayTrait;
use DateTimeInterface;

/**
 * This is just a DTO, it only has getters, theres no logic to test, so we ignore it for code coverage purposes.
 *
 * @codeCoverageIgnore
 */
final class PostWithAuthorAndTagsDto implements ConstructableFromArrayInterface
{
    use ConstructableFromArrayTrait;

    /**
     * @var PostId
     */
    private $postId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var DateTimeInterface
     */
    private $publishedAt;

    /**
     * @var string
     */
    private $summary;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $authorFullName;

    /**
     * @var string[]
     */
    private $tagList;

    /**
     * @throws \Exception
     */
    public function __construct(
        PostId $id,
        string $title,
        DateTimeInterface $publishedAt,
        string $summary,
        string $slug,
        string $content,
        string $authorFullName,
        string ...$tagList
    ) {
        $this->postId = $id;
        $this->title = $title;
        $this->publishedAt = $publishedAt;
        $this->summary = $summary;
        $this->slug = $slug;
        $this->content = $content;
        $this->authorFullName = $authorFullName;
        $this->tagList = $tagList;
    }

    public function getId(): PostId
    {
        return $this->postId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPublishedAt(): DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getAuthorFullName(): string
    {
        return $this->authorFullName;
    }

    /**
     * @return string[]
     */
    public function getTagList(): array
    {
        return $this->tagList;
    }
}
