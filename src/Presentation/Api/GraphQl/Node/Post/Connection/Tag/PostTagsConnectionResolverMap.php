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

namespace Acme\App\Presentation\Api\GraphQl\Node\Post\Connection\Tag;

use ArrayObject;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Relay\Connection\Output\Connection;
use Overblog\GraphQLBundle\Resolver\ResolverMap;

final class PostTagsConnectionResolverMap extends ResolverMap
{
    /**
     * @var PostTagsResolver
     */
    private $postTagsResolver;

    public function __construct(PostTagsResolver $postTagsResolver)
    {
        $this->postTagsResolver = $postTagsResolver;
    }

    public function map(): array
    {
        return [
            'PostTagsConnection' => [
                'count' => function (Connection $value, Argument $args, ArrayObject $context, ResolveInfo $info) {
                    return $this->postTagsResolver->countEdges($value);
                },
            ],
        ];
    }
}
