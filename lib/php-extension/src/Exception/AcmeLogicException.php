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

namespace Acme\PhpExtension\Exception;

use LogicException;

/**
 * Exception that represents an error in the program logic. This kind of
 * exceptions should directly lead to a fix in your code.
 *
 * In other words, these are exceptions that can happen, and should be
 * caught somewhere in our code and dealt with elegantly.
 * For example, when a user tries to register with a user name that already
 * exists, the persistence mechanism could throw a specific logic exception
 * which would be caught somewhere and show a nice error message to the user.
 *
 * This exception is in the PhpExtension, which means that it can be used in several projects of the same vendor.
 * Therefore, by catching this exception we might be catching an exception of another library created by
 * the same vendor.
 *
 * @see http://php.net/manual/en/class.logicexception.php
 */
class AcmeLogicException extends LogicException implements AcmeExceptionInterface
{
    use AcmeExceptionTrait;
}
