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

namespace Acme\App\Presentation\Api\GraphQl\Node\User;

use Acme\App\Presentation\Api\GraphQl\Node\User\Admin\AdminViewModel;
use Acme\App\Presentation\Api\GraphQl\Node\User\Editor\EditorViewModel;
use Acme\App\Presentation\Api\GraphQl\Node\User\Visitor\VisitorViewModel;
use Overblog\GraphQLBundle\Resolver\ResolverMap as BaseResolverMap;
use Overblog\GraphQLBundle\Resolver\TypeResolver;

final class UserResolverMap extends BaseResolverMap
{
    /**
     * @var TypeResolver
     */
    private $typeResolver;

    public function __construct(TypeResolver $typeResolver)
    {
        $this->typeResolver = $typeResolver;
    }

    protected function map(): array
    {
        return [
            'User' => [
                self::RESOLVE_TYPE => function ($value) {
                    switch (true) {
                        case $value instanceof AdminViewModel:
                            return $this->typeResolver->resolve('Admin');
                        case $value instanceof EditorViewModel:
                            return $this->typeResolver->resolve('Editor');
                        case $value instanceof VisitorViewModel:
                            return $this->typeResolver->resolve('Visitor');
                        default:
                            return null;
                    }
                },
            ],
        ];
    }
}
