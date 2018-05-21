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
final class PostDto implements ConstructableFromArrayInterface
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

    public function __construct(
        PostId $id,
        string $title,
        DateTimeInterface $publishedAt
    ) {
        $this->postId = $id;
        $this->title = $title;
        $this->publishedAt = $publishedAt;
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
}
