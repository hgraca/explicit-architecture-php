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

namespace Acme\App\Presentation\Api\GraphQl\Node\Comment\Connection\Author;

use ArrayObject;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Relay\Connection\Output\Connection;
use Overblog\GraphQLBundle\Resolver\ResolverMap;

final class CommentAuthorsConnectionResolverMap extends ResolverMap
{
    /**
     * @var CommentAuthorsResolver
     */
    private $commentAuthorsResolver;

    public function __construct(CommentAuthorsResolver $commentAuthorsResolver)
    {
        $this->commentAuthorsResolver = $commentAuthorsResolver;
    }

    public function map(): array
    {
        return [
            'CommentAuthorsConnection' => [
                'count' => function (Connection $value, Argument $args, ArrayObject $context, ResolveInfo $info) {
                    return $this->commentAuthorsResolver->countEdges($value);
                },
            ],
        ];
    }
}
