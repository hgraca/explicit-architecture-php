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

use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

class OauthAccessToken implements AccessTokenEntityInterface
{
    use AccessTokenTrait;
    use EntityTrait;
    use TokenEntityTrait;

    /**
     * @var bool
     */
    private $revoked = false;

    /**
     * @var DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var DateTimeImmutable
     */
    private $updatedAt;

    public function __construct(
        UserId $userId,
        ClientEntityInterface $client,
        array $scopes = [],
        DateTimeInterface $expiryDate = null
    ) {
        $this->setIdentifier(new OauthAccessTokenId());
        $this->setUserIdentifier($userId);
        $this->setClient($client);
        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }
        if ($expiryDate) {
            $this->setExpiryDateTime(
                $expiryDate instanceof DateTimeImmutable
                    ? DateTime::createFromImmutable($expiryDate)
                    : $expiryDate
            );
        }
    }

    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function revoke(): void
    {
        $this->revoked = true;
    }

    /**
     * It will only set the identifier if it's not yet set.
     */
    public function setIdentifier($identifier): void
    {
        $this->identifier = $this->identifier ?? $identifier;
    }
}
