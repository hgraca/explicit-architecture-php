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

namespace Acme\App\Infrastructure\Security;

use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;
use Acme\PhpExtension\ConstructableFromArrayInterface;
use Acme\PhpExtension\ConstructableFromArrayTrait;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class SecurityUser implements UserInterface, EquatableInterface, ConstructableFromArrayInterface
{
    use ConstructableFromArrayTrait;

    /**
     * @var UserId
     */
    private $userId;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string[]
     */
    private $roles;

    public function __construct(UserId $userId, string $username, string $password, array $roles = [])
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->password = $password;
        $this->roles = $roles;
    }

    public static function fromUser(User $user): self
    {
        return new self($user->getId(), $user->getUsername(), $user->getPassword() ?? '', $user->getRoles());
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @var string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        // See "Do you need to use a Salt?" at https://symfony.com/doc/current/cookbook/security/entity_provider.html
        // we're using bcrypt in security.yml to encode the password, so
        // the salt value is built-in and you don't have to generate one

        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        // if you had a plainPassword property, you'd nullify it here
        // $this->plainPassword = null;
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}
