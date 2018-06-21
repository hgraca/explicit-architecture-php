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

namespace Acme\App\Core\Component\User\Application\Service;

use Acme\App\Core\Component\User\Application\Repository\UserRepositoryInterface;
use Acme\App\Core\Component\User\Application\Validation\UserValidationService;
use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Core\Port\Persistence\Exception\EmptyQueryResultException;
use Acme\App\Core\Port\Validation\PhoneNumber\PhoneNumberException;
use Acme\App\Core\SharedKernel\Exception\AppRuntimeException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserService
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var UserValidationService
     */
    private $validator;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(
        UserPasswordEncoderInterface $encoder,
        UserValidationService $validator,
        UserRepositoryInterface $userRepository
    ) {
        $this->encoder = $encoder;
        $this->validator = $validator;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws PhoneNumberException
     */
    public function createUser(
        string $username,
        string $plainPassword,
        string $email,
        string $mobile,
        string $fullName,
        bool $isAdmin
    ): User {
        // make sure to validate the user data is correct
        $this->validateUserData($username, $plainPassword, $email, $mobile, $fullName);

        // See https://symfony.com/doc/current/book/security.html#security-encoding-password
        // create the user and encode its password
        $user = User::constructWithoutPassword(
            $username,
            $email,
            $mobile,
            $fullName,
            $isAdmin ? User::ROLE_ADMIN : User::ROLE_USER
        );
        $user->setPassword($this->encoder->encodePassword($user, $plainPassword));
        $this->userRepository->add($user);

        return $user;
    }

    public function deleteUser(string $username): void
    {
        $this->validator->validateUsername($username);

        /** @var User $user */
        $user = $this->userRepository->findOneByUsername($username);

        if ($user === null) {
            throw new AppRuntimeException(sprintf('User with username "%s" not found.', $username));
        }

        $this->userRepository->remove($user);
    }

    /**
     * @throws PhoneNumberException
     */
    private function validateUserData($username, $plainPassword, $email, $mobile, $fullName): void
    {
        // first check if a user with the same username already exists.
        if ($this->usernameExists($username)) {
            throw new AppRuntimeException("There is already a user registered with the \"$username\" username.");
        }

        // validate password and email if is not this input means interactive.
        $this->validator->validatePassword($plainPassword);
        $this->validator->validateEmail($email);
        $this->validator->validateMobile($mobile);
        $this->validator->validateFullName($fullName);

        // check if a user with the same email already exists.
        if ($this->emailExists($email)) {
            throw new AppRuntimeException("There is already a user registered with the \"$email\" email.");
        }
    }

    private function usernameExists(string $username): bool
    {
        try {
            $this->userRepository->findOneByUsername($username);

            return true;
        } catch (EmptyQueryResultException $e) {
            return false;
        }
    }

    private function emailExists(string $email): bool
    {
        try {
            $this->userRepository->findOneByEmail($email);

            return true;
        } catch (EmptyQueryResultException $e) {
            return false;
        }
    }
}
