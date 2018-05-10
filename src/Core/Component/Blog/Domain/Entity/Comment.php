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

namespace Acme\App\Core\Component\Blog\Domain\Entity;

use Acme\App\Core\Component\User\Domain\Entity\User;
use Acme\PhpExtension\DateTime\DateTimeGenerator;
use DateTimeImmutable;

/**
 * Defines the properties of the Comment entity to represent the blog comments.
 * See https://symfony.com/doc/current/book/doctrine.html#creating-an-entity-class.
 *
 * Tip: if you have an existing database, you can generate these entity class automatically.
 * See https://symfony.com/doc/current/cookbook/doctrine/reverse_engineering.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class Comment
{
    /**
     * @var CommentId
     */
    private $id;

    /**
     * @var Post
     */
    private $post;

    /**
     * @var string
     */
    private $content;

    /**
     * @var DateTimeImmutable
     */
    private $publishedAt;

    /**
     * @var User
     */
    private $author;

    public function __construct(string $content = '')
    {
        $this->publishedAt = DateTimeGenerator::generate();
        $this->id = new CommentId();
        $this->content = $content;
    }

    /**
     * Used for validation, in config/validator/validation.yaml:14
     */
    public function isLegitComment(): bool
    {
        $containsInvalidCharacters = mb_strpos($this->content, '@') !== false;

        return !$containsInvalidCharacters;
    }

    public function getId(): CommentId
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * This is needed for the $form->handleRequest() in the CommentController.
     * We shouldn't have this method here just because the framework needs it, but to remove it we need to change our
     * strategy for creating entities from forms. We will do that when we integrate a command bus.
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getPublishedAt(): DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    public function setPost(Post $post): void
    {
        $this->post = $post;
    }
}
