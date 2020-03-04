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

namespace Acme\App\Test\Framework\Database;

use Doctrine\ORM\EntityManagerInterface;

trait DatabaseAwareTestTrait
{
    abstract protected function getService(string $service);

    protected function clearDatabaseCache(): void
    {
        $this->getEntityManager()->clear();
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->getService(EntityManagerInterface::class);
    }
}
