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
use Acme\PhpExtension\String\Slugger;
use DateTime;

/**
 * Defines the properties of the Post entity to represent the blog posts.
 *
 * See https://symfony.com/doc/current/book/doctrine.html#creating-an-entity-class
 *
 * Tip: if you have an existing database, you can generate these entity class automatically.
 * See https://symfony.com/doc/current/cookbook/doctrine/reverse_engineering.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class Post
{
    /**
     * Use constants to define configuration options that rarely change instead
     * of specifying them under parameters section in config/services.yaml file.
     *
     * See https://symfony.com/doc/current/best_practices/configuration.html#constants-vs-configuration-options
     */
    const NUM_ITEMS = 10;

    /**
     * @var int
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
     * @var DateTime
     */
    private $publishedAt;

    /**
     * @var User
     */
    private $author;

    /**
     * @var Comment[]
     */
    private $comments = [];

    /**
     * @var Tag[]
     */
    private $tags = [];

    public function __construct()
    {
        $this->publishedAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function regenerateSlug(): void
    {
        $this->slug = Slugger::slugify($this->getTitle());
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getPublishedAt(): DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(DateTime $publishedAt): void
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

    /**
     * We don't want to have here any reference to doctrine, so we remove the Collection type hint from everywhere.
     * The safest is to treat it as an array but we can't type hint it with 'array' because we might actually
     * return an Collection.
     *
     * @return Comment[]
     */
    public function getComments()
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): void
    {
        $comment->setPost($this);
        if (!$this->contains($comment, $this->comments)) {
            $this->comments[] = $comment;
        }
    }

    public function removeComment(Comment $comment): void
    {
        $comment->setPost(null);

        if ($key = $this->getKey($comment, $this->comments)) {
            unset($this->comments[$key]);
        }
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): void
    {
        $this->summary = $summary;
    }

    public function addTag(Tag ...$tags): void
    {
        foreach ($tags as $tag) {
            if (!$this->contains($tag, $this->tags)) {
                $this->tags[] = $tag;
            }
        }
    }

    public function removeTag(Tag $tag): void
    {
        if ($key = $this->getKey($tag, $this->tags)) {
            unset($this->tags[$key]);
        }
    }

    /**
     * We don't want to have here any reference to doctrine, so we remove the Collection type hint from everywhere.
     * The safest is to treat it as an array but we can't type hint it with 'array' because we might actually
     * return an Collection.
     *
     * @return Tag[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Because we removed the doctrine `Collection` return type hint from `getTags()`, the `clear()` method below is
     *  not recognized by the IDE, as it is not even possible to call a method on an array.
     *
     * So we create this method here to encapsulate that operation, and minimize the issue.
     *
     * It is also a good practise to encapsulate these chained operations,
     * from an object calisthenics point of view.
     */
    public function clearTags(): void
    {
        $this->getTags()->clear();
    }

    private function contains($item, $list): bool
    {
        // we need to cast the list to array because it might just actually be a doctrine collection
        return \in_array($item, (array) $list, true);
    }

    /**
     * @return false|int|string
     */
    private function getKey($item, $list)
    {
        // we need to cast the list to array because it might just actually be a doctrine collection
        return \array_search($item, (array) $list, true);
    }
}
