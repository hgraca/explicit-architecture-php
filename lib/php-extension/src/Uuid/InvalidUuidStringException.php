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

namespace Acme\PhpExtension\Uuid;

use Acme\PhpExtension\Exception\AcmeLogicException;

final class InvalidUuidStringException extends AcmeLogicException
{
    public function __construct(string $uuid)
    {
        parent::__construct("Invalid Uuid string '$uuid'.");
    }
}
