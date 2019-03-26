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

namespace Acme\App\Presentation\Api\GraphQl;

use Acme\App\Core\Component\User\Application\Repository\UserRepositoryInterface;
use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Presentation\Api\GraphQl\Node\User\AbstractUserViewModel;
use Acme\App\Presentation\Api\GraphQl\Node\User\Admin\AdminViewModel;
use Acme\App\Presentation\Api\GraphQl\Node\User\Editor\EditorViewModel;
use Overblog\GraphQLBundle\Resolver\ResolverMap as BaseResolverMap;
use function array_map;

final class QueryResolverMap extends BaseResolverMap
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    protected function map(): array
    {
        return [
            'Query' => [
                'userList' => function () {
                    return array_map(
                        function (User $user) {
                            return $this->hydrateUserViewModel($user);
                        },
                        $this->userRepository->findAll()->toArray()
                    );
                },
            ],
        ];
    }

    private function hydrateUserViewModel(User $user): AbstractUserViewModel
    {
        if ($user->isAdmin()) {
            return AdminViewModel::constructFromEntity($user);
        }

        return EditorViewModel::constructFromEntity($user);
    }
}
