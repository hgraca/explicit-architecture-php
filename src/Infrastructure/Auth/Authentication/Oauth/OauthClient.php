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

namespace Acme\App\Infrastructure\Auth\Authentication\Oauth;

use Acme\PhpExtension\Uuid\UuidGenerator;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class OauthClient implements ClientEntityInterface
{
    use ClientTrait;
    use EntityTrait;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var bool
     */
    private $active = true;

    public function __construct(string $name, string $redirectUri)
    {
        $this->setIdentifier(new OauthClientId());
        $this->name = $name;
        $this->redirectUri = $redirectUri;
        $this->secret = UuidGenerator::generateAsString();
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
