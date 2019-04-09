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

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;

/**
 * Class OauthRefreshToken
 *
 * @method OauthAccessToken getAccessToken()
 */
class OauthRefreshToken implements RefreshTokenEntityInterface
{
    use EntityTrait;
    use RefreshTokenTrait;

    /**
     * @var bool
     */
    private $revoked = false;

    public function __construct()
    {
        $this->setIdentifier(new OauthRefreshTokenId());
    }

    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function revoke(): void
    {
        $this->revoked = true;
    }
}
