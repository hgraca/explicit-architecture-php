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

use Acme\App\Core\SharedKernel\Exception\AppRuntimeException;

final class InvalidOauthScopeException extends AppRuntimeException
{
    public function __construct(string $identifier)
    {
        parent::__construct("Could not find an Oauth scope with identifier '$identifier'");
    }
}
