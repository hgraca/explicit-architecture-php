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
use Acme\App\Core\Port\Security\UserSecretEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface as SymfonyUserPasswordEncoderInterface;

final class UserSecretEncoder implements UserSecretEncoderInterface
{
    /**
     * @var SymfonyUserPasswordEncoderInterface
     */
    private $symfonyEncoder;

    public function __construct(SymfonyUserPasswordEncoderInterface $symfonyEncoder)
    {
        $this->symfonyEncoder = $symfonyEncoder;
    }

    public function encode(string $secret, User $user): string
    {
        return $this->symfonyEncoder->encodePassword(SecurityUser::fromUser($user), $secret);
    }
}
