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
use Acme\App\Infrastructure\Auth\Authentication\Oauth\OauthAccessToken;
use Acme\PhpExtension\DateTime\DateTimeGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

class OauthAccessTokenFixtures extends Fixture implements DependentFixtureInterface
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

        $this->createToken($manager, UserFixtures::REFERENCE_ADMIN_TOM, OauthClientFixtures::CLIENT_WEB_APP, $scopes);
        $this->createToken(
            $manager,
            UserFixtures::REFERENCE_ADMIN_TOM,
            OauthClientFixtures::CLIENT_MOBILE_APP,
            $scopes
        );

        $manager->flush();
    }

    /**
     * @param string[] $scopes
     */
    protected function createToken(
        ObjectManager $manager,
        string $userReference,
        string $clientReference,
        array $scopes
    ): void {
        $token = new OauthAccessToken(
            $this->getUserReference($userReference)->getId(),
            $this->getClientReference($clientReference),
            $this->getScopeObjectList($scopes),
            DateTimeGenerator::generate('now + 1 year')
        );

        $manager->persist($token);
        $this->addReference($userReference . '_' . $clientReference, $token);
    }

    protected function getUserReference(string $userReference): User
    {
        return $this->getReference($userReference);
    }

    protected function getClientReference(string $clientReference): ClientEntityInterface
    {
        return $this->getReference($clientReference);
    }

    protected function getOauthScopeReference(string $oauthScopeReference): ScopeEntityInterface
    {
        return $this->getReference($oauthScopeReference);
    }

    /**
     * @param string[]
     *
     * @return ScopeEntityInterface[]
     */
    protected function getScopeObjectList(array $scopeList): array
    {
        $scopeObjectList = [];
        foreach ($scopeList as $scopeReference) {
            $scopeObjectList[] = $this->getOauthScopeReference($scopeReference);
        }

        return $scopeObjectList;
    }
}
