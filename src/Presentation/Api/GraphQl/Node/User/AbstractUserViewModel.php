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

use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Presentation\Api\GraphQl\Node\User\Admin\AdminViewModel;
use Acme\App\Presentation\Api\GraphQl\Node\User\Editor\EditorViewModel;

abstract class AbstractUserViewModel
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $fullName;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $mobile;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $type;

    protected function __construct(
        string $id,
        string $fullName,
        string $username,
        string $email,
        string $mobile,
        string $password,
        string $type
    ) {
        $this->id = $id;
        $this->fullName = $fullName;
        $this->username = $username;
        $this->email = $email;
        $this->mobile = $mobile;
        $this->password = $password;
        $this->type = $type;
    }

    /**
     * @return static
     */
    public static function constructFromEntity(User $user)
    {
        if ($user->isAdmin()) {
            return AdminViewModel::constructFromEntity($user);
        }

        return EditorViewModel::constructFromEntity($user);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getMobile(): string
    {
        return $this->mobile;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
