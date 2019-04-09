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

use Acme\App\Infrastructure\Auth\Authentication\Oauth\OauthScope;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

final class OauthScopeFixtures extends Fixture
{
    public const SCOPE_EDITOR = OauthScope::SCOPE_EDITOR;
    public const SCOPE_ADMIN = OauthScope::SCOPE_ADMIN;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $editorScope = new OauthScope(self::SCOPE_EDITOR);
        $manager->persist($editorScope);
        $this->addReference(self::SCOPE_EDITOR, $editorScope);

        $adminScope = new OauthScope(self::SCOPE_ADMIN);
        $manager->persist($adminScope);
        $this->addReference(self::SCOPE_ADMIN, $adminScope);

        $manager->flush();
    }
}
