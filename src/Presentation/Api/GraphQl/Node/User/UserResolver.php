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
use Acme\App\Presentation\Api\GraphQl\Node\User\Admin\AdminViewModel;
use Acme\App\Presentation\Api\GraphQl\Node\User\Editor\EditorViewModel;
use Acme\App\Presentation\Api\GraphQl\Node\User\Visitor\VisitorViewModel;

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

    public function getLoggedInUser(): ?AbstractUserViewModel
    {
        $user = $this->authenticationService->getLoggedInUser();

        if ($user->isAdmin()) {
            return AdminViewModel::constructFromEntity($user);
        }

        if ($user->isUser()) {
            return EditorViewModel::constructFromEntity($user);
        }

        return VisitorViewModel::constructFromEntity($user);
    }
}
