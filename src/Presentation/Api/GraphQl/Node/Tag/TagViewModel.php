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

namespace Acme\App\Presentation\Api\GraphQl\Node\Tag;

use Acme\App\Core\Component\Blog\Domain\Post\Tag\Tag;

final class TagViewModel
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    private function __construct(
        string $id,
        string $name
    ) {
        $this->id = $id;
        $this->name = $name;
    }

    public static function constructFromEntity(Tag $tag): self
    {
        return new self(
            $tag->getId()->toScalar(),
            $tag->getName()
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
