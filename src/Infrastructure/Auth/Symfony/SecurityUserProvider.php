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

namespace Acme\App\Infrastructure\Auth\Symfony;

use Acme\App\Core\SharedKernel\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class SecurityUserProvider implements UserProviderInterface
{
    /**
     * @var SecurityUserQuery
     */
    private $securityUserQuery;

    public function __construct(SecurityUserQuery $securityUserQuery)
    {
        $this->securityUserQuery = $securityUserQuery;
    }

    public function loadUserByUsername($username)
    {
        return $this->securityUserQuery->execute($username);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof SecurityUser) {
            throw new InvalidArgumentException(sprintf('Instances of \'%s\' are not supported.', \get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class): bool
    {
        return $class === SecurityUser::class;
    }
}
