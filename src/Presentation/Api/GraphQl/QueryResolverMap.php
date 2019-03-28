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

use Acme\App\Core\Component\Blog\Application\Repository\PostRepositoryInterface;
use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Component\User\Application\Repository\UserRepositoryInterface;
use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;
use Acme\App\Presentation\Api\GraphQl\Node\Post\PostViewModel;
use Acme\App\Presentation\Api\GraphQl\Node\User\AbstractUserViewModel;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Resolver\ResolverMap as BaseResolverMap;
use function array_map;

final class QueryResolverMap extends BaseResolverMap
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        PostRepositoryInterface $postRepository
    ) {
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;
    }

    protected function map(): array
    {
        return [
            'Query' => [
                'postList' => function () {
                    return array_map(
                        function (Post $post) {
                            return PostViewModel::constructFromEntity($post);
                        },
                        $this->postRepository->findAll()->toArray()
                    );
                },
                'user' => function ($value, Argument $args) {
                    $user = $this->userRepository->findOneById(new UserId($args['id']));

                    return AbstractUserViewModel::constructFromEntity($user);
                },
                'userList' => function () {
                    return array_map(
                        function (User $user) {
                            return AbstractUserViewModel::constructFromEntity($user);
                        },
                        $this->userRepository->findAll()->toArray()
                    );
                },
            ],
        ];
    }
}
