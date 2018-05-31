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

namespace Acme\App\Infrastructure\Notification\Strategy;

use Acme\App\Core\SharedKernel\Exception\AppLogicException;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Nicolae Nichifor
 */
final class InvalidGeneratorException extends AppLogicException
{
    public function __construct(string $generatorClass, string $generatorMethod)
    {
        parent::__construct('Generator class ' . $generatorClass . ' does not have a method ' . $generatorMethod);
    }
}
