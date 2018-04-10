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
final class PostsBySearchRequestDto implements ConstructableFromArrayInterface
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
    private $fullName;

    /**
     * @throws \Exception
     */
    public function __construct(
        string $title,
        DateTimeInterface $publishedAt,
        string $summary,
        string $slug,
        string $fullName
    ) {
        $this->title = $title;
        $this->publishedAt = $publishedAt;
        $this->summary = $summary;
        $this->slug = $slug;
        $this->fullName = $fullName;
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

    public function getFullName(): string
    {
        return $this->fullName;
    }
}
