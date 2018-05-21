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

use Acme\PhpExtension\ConstructableFromArrayInterface;
use Acme\PhpExtension\ConstructableFromArrayTrait;
use DateTimeInterface;

/**
 * This is just a DTO, it only has getters, theres no logic to test, so we ignore it for code coverage purposes.
 *
 * @codeCoverageIgnore
 */
final class LatestPostWithAuthorAndTagsDto implements ConstructableFromArrayInterface
{
    use ConstructableFromArrayTrait;

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
    private $authorFullName;

    /**
     * @var string[]
     */
    private $tagList;

    /**
     * @var string
     */
    private $authorEmail;

    /**
     * @throws \Exception
     */
    public function __construct(
        string $title,
        DateTimeInterface $publishedAt,
        string $summary,
        string $slug,
        string $authorFullName,
        string $authorEmail,
        string ...$tagList
    ) {
        $this->title = $title;
        $this->publishedAt = $publishedAt;
        $this->summary = $summary;
        $this->slug = $slug;
        $this->authorFullName = $authorFullName;
        $this->authorEmail = $authorEmail;
        $this->tagList = $tagList;
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

    public function getAuthorFullName(): string
    {
        return $this->authorFullName;
    }

    public function getAuthorEmail(): string
    {
        return $this->authorEmail;
    }

    /**
     * @return string[]
     */
    public function getTagList(): array
    {
        return $this->tagList;
    }
}
