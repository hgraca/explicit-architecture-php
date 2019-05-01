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

namespace Acme\App\Test\Fixture\Doctrine;

use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Infrastructure\Auth\Authentication\SecurityUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Defines the sample userRepository to load in the database before running the unit and
 * functional tests. Execute this command to load the data.
 *
 *   $ php bin/console doctrine:fixtures:load
 *
 * See https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class UserFixtures extends Fixture
{
    public const REFERENCE_ADMIN_JANE = 'jane-admin';
    public const REFERENCE_USER_JOHN = 'john-user';
    public const JANE_EMAIL = 'jane_admin@symfony.com';
    public const JOHN_EMAIL = 'john_user@symfony.com';
    public const JANE_MOBILE = '+31631769212';
    public const JOHN_MOBILE = '+31631769213';

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $janeAdmin = User::constructWithoutPassword(
            'jane_admin',
            self::JANE_EMAIL,
            self::JANE_MOBILE,
            'Jane Doe',
            User::ROLE_ADMIN
        );
        $encodedPassword = $this->passwordEncoder->encodePassword(SecurityUser::fromUser($janeAdmin), 'kitten');
        $janeAdmin->setPassword($encodedPassword);
        $manager->persist($janeAdmin);
        // In case if fixture objects have relations to other fixtures, adds a reference
        // to that object by name and later reference it to form a relation.
        // See https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html#sharing-objects-between-fixtures
        $this->addReference(self::REFERENCE_ADMIN_JANE, $janeAdmin);

        $johnUser = User::constructWithoutPassword(
            'john_user',
            self::JOHN_EMAIL,
            self::JOHN_MOBILE,
            'John Doe',
            User::ROLE_USER
        );
        $encodedPassword = $this->passwordEncoder->encodePassword(SecurityUser::fromUser($johnUser), 'kitten');
        $johnUser->setPassword($encodedPassword);
        $manager->persist($johnUser);
        $this->addReference(self::REFERENCE_USER_JOHN, $johnUser);

        $manager->flush();
    }
}
