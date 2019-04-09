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

namespace Acme\App\Core\Port\Auth\Authentication;

use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;
use Psr\Http\Message\ServerRequestInterface;

interface AuthenticationServiceInterface
{
    public function isCsrfTokenValid(string $id, string $token): bool;

    public function getLoggedInUserId(): UserId;

    public function getLoggedInUser(): User;

    public function getLastAuthenticationError(
        ServerRequestInterface $request,
        bool $clearSession = true
    ): ?AuthenticationException;

    public function getLastAuthenticationUsername(ServerRequestInterface $request): string;
}
