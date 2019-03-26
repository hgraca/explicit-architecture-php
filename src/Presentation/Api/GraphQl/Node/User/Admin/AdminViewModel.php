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

namespace Acme\App\Presentation\Api\GraphQl\Node\User\Admin;

use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Presentation\Api\GraphQl\Node\User\AbstractUserViewModel;

final class AdminViewModel extends AbstractUserViewModel
{
    /**
     * @var string
     */
    private $adminViewModelClass = __CLASS__;

    public static function constructFromEntity(User $user): self
    {
        return new static(
            $user->getId()->toScalar(),
            $user->getFullName(),
            $user->getUsername(),
            $user->getEmail(),
            $user->getMobile(),
            $user->getPassword(),
            'Admin'
        );
    }

    public function getAdminViewModelClass(): string
    {
        return $this->adminViewModelClass;
    }
}
