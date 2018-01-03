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

namespace Acme\StdLib\Exception;

use RuntimeException;

/**
 * Exception thrown if an error which can only be found at runtime occurs.
 * During normal running of the application this exceptions should not occur.
 * So if they occur, there is a bug somewhere.
 *
 * In other words, these exceptions should never actually be thrown,
 * if they do then there is an error somewhere.
 * They are used only to signal the developers that there is a bug somewhere,
 * so they should not be caught, they should result in an application error.
 *
 * This exception is in the StdLib, which means that it can be used in several projects of the same vendor.
 * Therefore, by catching this exception we might be catching an exception of another library created by
 * the same vendor.
 *
 * @see http://php.net/manual/en/class.runtimeexception.php
 */
class AcmeRuntimeException extends RuntimeException implements AcmeExceptionInterface
{
    use AcmeExceptionTrait;
}
