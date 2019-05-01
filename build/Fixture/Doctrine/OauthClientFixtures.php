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

use Acme\App\Infrastructure\Auth\Authentication\Oauth\OauthClient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

final class OauthClientFixtures extends Fixture
{
    const CLIENT_WEB_APP = 'web_app';
    const CLIENT_MOBILE_APP = 'mobile_app';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $webApp = new OauthClient(self::CLIENT_WEB_APP, '');
        $manager->persist($webApp);
        $this->addReference(self::CLIENT_WEB_APP, $webApp);

        $mobileApp = new OauthClient(self::CLIENT_MOBILE_APP, '');
        $manager->persist($mobileApp);
        $this->addReference(self::CLIENT_MOBILE_APP, $mobileApp);

        $manager->flush();
    }
}
