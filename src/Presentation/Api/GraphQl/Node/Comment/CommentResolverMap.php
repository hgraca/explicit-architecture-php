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

use Acme\App\Core\SharedKernel\Component\Blog\Domain\Post\Comment\CommentId;
use Acme\App\Presentation\Api\GraphQl\Node\Comment\Connection\Author\CommentAuthorsResolver;
use ArrayObject;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Resolver\ResolverMap as BaseResolverMap;

final class CommentResolverMap extends BaseResolverMap
{
    /**
     * @var CommentAuthorsResolver
     */
    private $commentAuthorsResolver;

    public function __construct(CommentAuthorsResolver $commentAuthorsResolver)
    {
        $this->commentAuthorsResolver = $commentAuthorsResolver;
    }

    protected function map(): array
    {
        return [
            'Comment' => [
                'authors' => function (
                    CommentViewModel $value,
                    Argument $args,
                    ArrayObject $context,
                    ResolveInfo $info
                ) {
                    return $this->commentAuthorsResolver->getCommentAuthorsConnection(new CommentId($value->getId()));
                },
            ],
        ];
    }
}
