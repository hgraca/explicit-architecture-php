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

namespace Acme\App\Presentation\Api\GraphQl\Node\User\Editor;

use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;
use Acme\App\Presentation\Api\GraphQl\Node\User\AbstractUserViewModel;
use Acme\App\Presentation\Api\GraphQl\Node\User\CreatedPosts\CreatedPostsResolver;
use ArrayObject;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Resolver\ResolverMap as BaseResolverMap;

final class EditorResolverMap extends BaseResolverMap
{
    /**
     * @var CreatedPostsResolver
     */
    private $createdPostsResolver;

    public function __construct(CreatedPostsResolver $createdPostsResolver)
    {
        $this->createdPostsResolver = $createdPostsResolver;
    }

    protected function map(): array
    {
        return [
            'Editor' => [
                'createdPosts' => function (
                    AbstractUserViewModel $value,
                    Argument $args,
                    ArrayObject $context,
                    ResolveInfo $info
                ) {
                    return $this->createdPostsResolver->getCreatedPostsConnection(new UserId($args['id']));
                },
            ],
        ];
    }
}
