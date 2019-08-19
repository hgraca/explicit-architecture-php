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

namespace Acme\App\Core\Component\User\Domain\User;

use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;

/**
 * Defines the properties of the User entity to represent the application users.
 * See https://symfony.com/doc/current/book/doctrine.html#creating-an-entity-class.
 *
 * Tip: if you have an existing database, you can generate these entity class automatically.
 * See https://symfony.com/doc/current/cookbook/doctrine/reverse_engineering.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class User implements \Serializable
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_USER = 'ROLE_USER';

    /**
     * @var UserId
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
     * @var array
     */
    private $roles = [];

    private function __construct()
    {
        $this->id = new UserId();
    }

    public static function constructWithoutPassword(
        string $username,
        string $email,
        string $mobile,
        string $fullName,
        string $role
    ): self {
        $user = new self();
        $user->setFullName($fullName);
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setMobile($mobile);
        $user->setRoles([$role]);

        return $user;
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getMobile(): string
    {
        return $this->mobile;
    }

    public function hasMobile(): bool
    {
        return !empty($this->mobile);
    }

    public function setMobile(string $mobile): void
    {
        $this->mobile = $mobile;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * Returns the roles or permissions granted to the user for security.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = self::ROLE_USER;
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        return serialize([$this->id, $this->username, $this->password]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        [$this->id, $this->username, $this->password] = unserialize($serialized, [UserId::class]);
    }

    public function isAdmin(): bool
    {
        foreach ($this->getRoles() as $role) {
            if ($role === self::ROLE_ADMIN) {
                return true;
            }
        }

        return false;
    }

    public function isUser(): bool
    {
        foreach ($this->getRoles() as $role) {
            if ($role === self::ROLE_USER) {
                return true;
            }
        }

        return false;
    }
}
