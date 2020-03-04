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

namespace Acme\App\Test\Framework\Container;

use Acme\App\Test\Framework\CompilerPass\CreateTestContainer\CreateTestContainerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

trait ContainerAwareTestTrait
{
    abstract protected function getContainer(): ContainerInterface;

    /**
     * @after
     */
    protected function tearDownKernel(): void
    {
        if (static::$kernel) {
            static::$kernel = null;
        }
    }

    protected function getTestContainer(): ServiceLocator
    {
        // the service container is always available via the test client
        /** @var ServiceLocator $testContainer */
        $testContainer = $this->getContainer()->get(CreateTestContainerCompilerPass::TEST_CONTAINER);

        return $testContainer;
    }

    protected function getService(string $service)
    {
        return $this->getTestContainer()->get($service);
    }

    protected function setService(string $service, $object): void
    {
        $this->getContainer()->set($service, $object);
    }

    protected function getParameter(string $parameter)
    {
        return $this->getContainer()->getParameter($parameter);
    }

    protected function hasParameter(string $parameter): bool
    {
        return $this->getContainer()->hasParameter($parameter);
    }
}
