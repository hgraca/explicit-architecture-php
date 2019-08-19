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

namespace Acme\App\Presentation\Api\GraphQl\Node\User\Connection\CreatedPosts;

use ArrayObject;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Relay\Connection\Output\Edge;
use Overblog\GraphQLBundle\Resolver\ResolverMap;

final class CreatedPostsEdgeResolverMap extends ResolverMap
{
    public function map(): array
    {
        return [
            'CreatedPostsEdge' => [
                'bar' => function (Edge $value, Argument $args, ArrayObject $context, ResolveInfo $info) {
                    return 'Some metadata about the post created by the user, contained in this edge.';
                },
            ],
        ];
    }
}
