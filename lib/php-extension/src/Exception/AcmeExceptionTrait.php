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

use Acme\PhpExtension\Helper\ClassHelper;
use Throwable;

trait AcmeExceptionTrait
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message ?: ClassHelper::extractCanonicalClassName(static::class), $code, $previous);
    }
}
