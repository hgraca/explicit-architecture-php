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

namespace Acme\App\Presentation\Web\Infrastructure\Auth\Symfony;

use Acme\App\Core\Component\User\Domain\Entity\User;
use Acme\App\Presentation\Web\Auth\NoUserAuthenticatedException;
use Acme\App\Presentation\Web\Core\Port\Auth\AuthenticationServiceInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class AuthenticationService implements AuthenticationServiceInterface
{
    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(
        CsrfTokenManagerInterface $csrfTokenManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function isCsrfTokenValid(string $id, string $token): bool
    {
        return $this->csrfTokenManager->isTokenValid(new CsrfToken($id, $token));
    }

    public function getLoggedInUserId(): int
    {
        return $this->getLoggedInUser()->getId();
    }

    public function getLoggedInUser(): User
    {
        $token = $this->tokenStorage->getToken();

        if ($token === null || !\is_object($user = $token->getUser())) {
            throw new NoUserAuthenticatedException();
        }

        return $user;
    }
}
