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

namespace Acme\App\Core\Port\Persistence\Exception;

use Acme\App\Core\Port\Persistence\QueryInterface;
use Acme\App\Core\SharedKernel\Exception\AppRuntimeException;

final class UnableToHandleQueryException extends AppRuntimeException
{
    public function __construct(QueryInterface $query)
    {
        parent::__construct('Unable to handle query of type ' . \get_class($query));
    }
}
