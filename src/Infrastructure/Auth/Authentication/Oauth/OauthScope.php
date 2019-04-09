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

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class OauthScope implements ScopeEntityInterface
{
    use EntityTrait;

    public const SCOPE_EDITOR = 'editor';
    public const SCOPE_ADMIN = 'admin';

    /**
     * @var array
     */
    public static $scopes = [
        self::SCOPE_ADMIN,
        self::SCOPE_EDITOR,
    ];

    public function __construct(string $identifier)
    {
        if (!self::hasScope($identifier)) {
            throw new InvalidOauthScopeException($identifier);
        }

        $this->setIdentifier($identifier);
    }

    public static function constructEditorScope(): self
    {
        return new self(self::SCOPE_EDITOR);
    }

    public static function constructAdminScope(): self
    {
        return new self(self::SCOPE_ADMIN);
    }

    public static function hasScope(string $identifier): bool
    {
        return $identifier === '*' || array_key_exists($identifier, array_flip(static::$scopes));
    }

    public function jsonSerialize(): string
    {
        return $this->getIdentifier();
    }
}
