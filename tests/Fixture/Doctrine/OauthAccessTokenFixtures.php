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

use Acme\App\Build\Fixture\Doctrine\OauthAccessTokenFixtures as ProductionOauthAccessTokenFixtures;
use Acme\App\Build\Fixture\Doctrine\OauthClientFixtures;
use Acme\App\Build\Fixture\Doctrine\OauthScopeFixtures;
use Doctrine\Common\Persistence\ObjectManager;

final class OauthAccessTokenFixtures extends ProductionOauthAccessTokenFixtures
{
    /**
     * Instead of defining the exact order in which the fixtures files must be loaded,
     * this method defines which other fixtures this file depends on. Then, Doctrine
     * will figure out the best order to fit all the dependencies.
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            OauthClientFixtures::class,
            OauthScopeFixtures::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $scopes = [OauthScopeFixtures::SCOPE_ADMIN, OauthScopeFixtures::SCOPE_EDITOR];
        $this->createToken($manager, UserFixtures::REFERENCE_ADMIN_JANE, OauthClientFixtures::CLIENT_WEB_APP, $scopes);
        $this->createToken(
            $manager,
            UserFixtures::REFERENCE_ADMIN_JANE,
            OauthClientFixtures::CLIENT_MOBILE_APP,
            $scopes
        );

        $scopes = [OauthScopeFixtures::SCOPE_EDITOR];
        $this->createToken($manager, UserFixtures::REFERENCE_USER_JOHN, OauthClientFixtures::CLIENT_WEB_APP, $scopes);
        $this->createToken(
            $manager,
            UserFixtures::REFERENCE_USER_JOHN,
            OauthClientFixtures::CLIENT_MOBILE_APP,
            $scopes
        );

        $manager->flush();
    }
}
