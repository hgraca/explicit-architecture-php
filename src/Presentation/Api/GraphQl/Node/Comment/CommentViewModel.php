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

namespace Acme\App\Presentation\Api\GraphQl\Node\Comment;

use Acme\App\Core\Component\Blog\Domain\Post\Comment\Comment;
use DateTimeInterface;

final class CommentViewModel
{
    /**
     * @var string
     */
    private $id;

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
        string $content,
        DateTimeInterface $publishedAt
    ) {
        $this->id = $id;
        $this->content = $content;
        $this->publishedAt = $publishedAt;
    }

    public static function constructFromEntity(Comment $comment): self
    {
        return new self(
            $comment->getId()->toScalar(),
            $comment->getContent(),
            $comment->getPublishedAt()
        );
    }

    public function getId(): string
    {
        return $this->id;
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
