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

use Acme\App\Core\Port\Auth\AccessDeniedException;
use Acme\App\Core\Port\Auth\AuthorizationServiceInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class AuthorizationService implements AuthorizationServiceInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function hasRole(string ...$roleList): bool
    {
        return $this->authorizationChecker->isGranted($roleList);
    }

    public function isAllowed(string $action, $subject): bool
    {
        return $this->authorizationChecker->isGranted($action, $subject);
    }

    /**
     * Throws an exception unless the specified roles and action are met on the subject.
     */
    public function denyAccessUnlessGranted(
        array $roleList = [],
        string $action = '',
        string $message = 'Access Denied.',
        $subject = null
    ): void {
        $attributes[] = $action;
        $attributes = \array_filter(\array_merge($roleList, $attributes));

        if (!$this->authorizationChecker->isGranted($attributes, $subject)) {
            throw new AccessDeniedException($roleList, $action, $subject, $message);
        }
    }
}
