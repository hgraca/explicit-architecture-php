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

use Ramsey\Uuid\Uuid as RamseyUuid;

final class Uuid
{
    /**
     * @var string
     */
    private $uuid;

    public function __construct(string $uuid)
    {
        if (!self::isValid($uuid)) {
            throw new InvalidUuidStringException($uuid);
        }

        $this->uuid = $uuid;
    }

    public function __toString(): string
    {
        return $this->uuid;
    }

    public static function isValid(string $uuid): bool
    {
        return RamseyUuid::isValid($uuid);
    }
}
