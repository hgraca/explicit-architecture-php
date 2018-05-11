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

use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Core\Component\User\Domain\User\UserId;
use Acme\App\Presentation\Web\Core\Port\Auth\AuthenticationException;
use Acme\App\Presentation\Web\Core\Port\Auth\AuthenticationServiceInterface;
use Acme\App\Presentation\Web\Core\Port\Auth\NoUserAuthenticatedException;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException as SymfonyAuthenticationException;
use Symfony\Component\Security\Core\Security;
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

    /**
     * @var HttpFoundationFactoryInterface
     */
    private $symfonyRequestFactory;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(
        CsrfTokenManagerInterface $csrfTokenManager,
        TokenStorageInterface $tokenStorage,
        HttpFoundationFactoryInterface $symfonyRequestFactory,
        SessionInterface $session
    ) {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->tokenStorage = $tokenStorage;
        $this->symfonyRequestFactory = $symfonyRequestFactory;
        $this->session = $session;
    }

    public function isCsrfTokenValid(string $id, string $token): bool
    {
        return $this->csrfTokenManager->isTokenValid(new CsrfToken($id, $token));
    }

    public function getLoggedInUserId(): UserId
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

    public function getLastAuthenticationError(
        ServerRequestInterface $request,
        bool $clearSession = true
    ): ?AuthenticationException {
        $request = $this->symfonyRequestFactory->createRequest($request);
        $session = $this->session;
        /** @var null|SymfonyAuthenticationException $symfonyAuthenticationException */
        $symfonyAuthenticationException = null;

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $symfonyAuthenticationException = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } elseif ($session !== null && $session->has(Security::AUTHENTICATION_ERROR)) {
            $symfonyAuthenticationException = $session->get(Security::AUTHENTICATION_ERROR);

            if ($clearSession) {
                $session->remove(Security::AUTHENTICATION_ERROR);
            }
        }

        return $symfonyAuthenticationException
            ? new AuthenticationException(
                $symfonyAuthenticationException->getMessage(),
                $symfonyAuthenticationException->getCode(),
                $symfonyAuthenticationException,
                $symfonyAuthenticationException->getMessageKey(),
                $symfonyAuthenticationException->getMessageData()
            )
            : null;
    }

    public function getLastAuthenticationUsername(ServerRequestInterface $request): string
    {
        $request = $this->symfonyRequestFactory->createRequest($request);
        if ($request->attributes->has(Security::LAST_USERNAME)) {
            return $request->attributes->get(Security::LAST_USERNAME, '');
        }

        return $this->session === null ? '' : $this->session->get(Security::LAST_USERNAME, '');
    }
}
