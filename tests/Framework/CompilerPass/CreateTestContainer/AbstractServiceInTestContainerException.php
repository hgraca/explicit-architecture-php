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

namespace Acme\App\Test\Framework\CompilerPass\CreateTestContainer;

use Exception;

final class AbstractServiceInTestContainerException extends Exception
{
    public function __construct(string $serviceId)
    {
        parent::__construct("Service '$serviceId' is in the test container but it is abstract.");
    }
}
