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

namespace Acme\App\Core\Port\Validation\PhoneNumber;

use Acme\App\Core\SharedKernel\Exception\AppRuntimeException;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Nicolae Nichifor
 */
class PhoneNumberCouldNotBeParsedException extends AppRuntimeException implements PhoneNumberException
{
    public function __construct(string $phoneNumber)
    {
        parent::__construct("Phone number '$phoneNumber' could not be parsed");
    }
}
