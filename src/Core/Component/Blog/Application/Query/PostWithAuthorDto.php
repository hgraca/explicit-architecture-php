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
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;
use Acme\PhpExtension\ConstructableFromArrayInterface;
use Acme\PhpExtension\ConstructableFromArrayTrait;

/**
 * This is just a DTO, it only has getters, theres no logic to test, so we ignore it for code coverage purposes.
 *
 * @codeCoverageIgnore
 */
final class PostWithAuthorDto implements ConstructableFromArrayInterface
{
    use ConstructableFromArrayTrait;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var UserId
     */
    private $authorId;

    /**
     * @var string
     */
    private $authorMobile;

    /**
     * @var string
     */
    private $authorFullName;

    /**
     * @var string
     */
    private $authorEmail;

    /**
     * @var PostId
     */
    private $id;

    /**
     * @throws \Exception
     */
    public function __construct(
        PostId $id,
        string $title,
        string $slug,
        UserId $authorId,
        string $authorMobile,
        string $authorFullName,
        string $authorEmail
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->authorId = $authorId;
        $this->authorMobile = $authorMobile;
        $this->authorFullName = $authorFullName;
        $this->authorEmail = $authorEmail;
    }

    public function getId(): PostId
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

    public function getAuthorId(): UserId
    {
        return $this->authorId;
    }

    public function getAuthorMobile(): string
    {
        return $this->authorMobile;
    }

    public function getAuthorFullName(): string
    {
        return $this->authorFullName;
    }

    public function getAuthorEmail(): string
    {
        return $this->authorEmail;
    }
}
