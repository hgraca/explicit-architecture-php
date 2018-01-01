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

namespace Acme\App\Test\TestCase\Core\Port\Persistence;

use Acme\App\Core\Port\Persistence\QueryInterface;
use Acme\App\Core\Port\Persistence\QueryServiceInterface;
use Acme\App\Core\Port\Persistence\ResultCollection;
use Acme\App\Core\Port\Persistence\ResultCollectionInterface;

final class DummyQueryService implements QueryServiceInterface
{
    /**
     * @var string
     */
    private $canHandle;

    /**
     * @var null|string
     */
    private $invokeWasCalledWith;

    public function __construct(string $canHandle)
    {
        $this->canHandle = $canHandle;
    }

    public function __invoke(QueryInterface $query): ResultCollectionInterface
    {
        $this->invokeWasCalledWith = \get_class($query);

        return new ResultCollection();
    }

    public function canHandle(): string
    {
        return $this->canHandle;
    }

    public function getInvokeWasCalledWith(): ?string
    {
        return $this->invokeWasCalledWith;
    }
}
