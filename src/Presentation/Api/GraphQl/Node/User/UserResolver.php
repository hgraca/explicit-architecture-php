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

use Acme\App\Core\Port\Auth\Authentication\AuthenticationServiceInterface;
use Acme\App\Core\Port\Auth\Authentication\NoUserAuthenticatedException;
use Acme\App\Core\SharedKernel\Exception\AppRuntimeException;
use Acme\App\Presentation\Api\GraphQl\Node\User\Admin\AdminViewModel;
use Acme\App\Presentation\Api\GraphQl\Node\User\Editor\EditorViewModel;
use Acme\App\Presentation\Api\GraphQl\Node\User\Visitor\VisitorViewModel;
use function json_encode;

class UserResolver
{
    /**
     * @var AuthenticationServiceInterface
     */
    private $authenticationService;

    public function __construct(AuthenticationServiceInterface $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function getUser(): AbstractUserViewModel
    {
        try {
            $user = $this->authenticationService->getLoggedInUser();
        } catch (NoUserAuthenticatedException $e) {
            return VisitorViewModel::construct();
        }

        if ($user->isAdmin()) {
            return AdminViewModel::constructFromEntity($user);
        }

        if ($user->isEditor()) {
            return EditorViewModel::constructFromEntity($user);
        }

        throw new AppRuntimeException('Unknown user type, with roles: ' . json_encode($user->getRoles()));
    }
}
