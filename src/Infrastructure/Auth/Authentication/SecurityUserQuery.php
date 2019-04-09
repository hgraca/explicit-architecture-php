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

namespace Acme\App\Infrastructure\Auth\Authentication;

use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;

final class SecurityUserQuery
{
    /**
     * @var DqlQueryBuilderInterface
     */
    private $dqlQueryBuilder;

    /**
     * @var QueryServiceRouterInterface
     */
    private $queryService;

    public function __construct(
        DqlQueryBuilderInterface $dqlQueryBuilder,
        QueryServiceRouterInterface $queryService
    ) {
        $this->dqlQueryBuilder = $dqlQueryBuilder;
        $this->queryService = $queryService;
    }

    public function execute(string $username): SecurityUser
    {
        $this->dqlQueryBuilder->create(User::class, 'User')
            ->select(
                'User.id AS userId',
                'User.username AS username',
                'User.password AS password',
                'User.roles AS roles'
            )
            ->where('User.username = :username')
            ->setParameter('username', $username);

        return $this->queryService->query($this->dqlQueryBuilder->build())->hydrateSingleResultAs(SecurityUser::class);
    }
}
