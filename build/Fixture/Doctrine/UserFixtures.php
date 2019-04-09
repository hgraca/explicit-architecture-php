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

namespace Acme\App\Build\Fixture\Doctrine;

use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Infrastructure\Auth\Authentication\SecurityUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Defines the sample users to load in the database before a production deployment is ready.
 * Execute this command to load the data.
 *
 *   $ php bin/console doctrine:fixtures:load
 *
 * See https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html
 *
 * @author Herberto Graça <herberto.graca@gmail.com>
 */
class UserFixtures extends Fixture
{
    public const TOM_MOBILE = '+31631769211';

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
        $tomAdmin = User::constructWithoutPassword(
            'tom_admin',
            'tom_admin@symfony.com',
            self::TOM_MOBILE,
            'Tom Doe',
            User::ROLE_ADMIN
        );
        $encodedPassword = $this->passwordEncoder->encodePassword(SecurityUser::fromUser($tomAdmin), 'kitten');
        $tomAdmin->setPassword($encodedPassword);
        $manager->persist($tomAdmin);
        // In case if fixture objects have relations to other fixtures, adds a reference
        // to that object by name and later reference it to form a relation.
        // See https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html#sharing-objects-between-fixtures
        $this->addReference('tom-admin', $tomAdmin);

        $manager->flush();
    }
}
