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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * In order to be able to use private services in our tests, we need to create a test container with all the services
 *  that we need in the tests.
 * The compiler pass gets these services from the parameter self::TEST_CONTAINER_SERVICE_LIST,
 *  which is an array with the services names that should be in this container.
 *
 * @see https://github.com/symfony/symfony-docs/issues/8097#issuecomment-348963302
 */
final class CreateTestContainerCompilerPass implements CompilerPassInterface
{
    public const TEST_CONTAINER_SERVICE_LIST = 'test.container.service_list';
    public const TEST_CONTAINER = 'test.container';
    public const CONTAINER_SERVICE_LOCATOR = 'container.service_locator';

    public function process(ContainerBuilder $containerBuilder): void
    {
        if (!$containerBuilder->hasParameter(self::TEST_CONTAINER_SERVICE_LIST)) {
            return;
        }

        $services = array_merge(
            $this->getServicesConfiguredToBeInTheTestContainer($containerBuilder),
            $this->getPublicServices($containerBuilder)
        );

        $containerBuilder->register(self::TEST_CONTAINER, ServiceLocator::class)
            ->setPublic(true)
            ->addTag(self::CONTAINER_SERVICE_LOCATOR)
            ->setArguments(
                [
                    array_combine(
                        $services,
                        array_map(
                            function (string $id) use ($containerBuilder): Reference {
                                if (!$containerBuilder->has($id)) {
                                    throw new ServiceInTestContainerNotFoundInProductionContainerException($id);
                                }

                                if ($containerBuilder->findDefinition($id)->isAbstract()) {
                                    throw new AbstractServiceInTestContainerException($id);
                                }

                                return new Reference($id);
                            },
                            $services
                        )
                    ),
                ]
            );
    }

    /**
     * @return string[]
     */
    private function getServicesConfiguredToBeInTheTestContainer(ContainerBuilder $containerBuilder): array
    {
        $testContainerServiceList = $containerBuilder->getParameter(self::TEST_CONTAINER_SERVICE_LIST);

        if (\count(array_unique($testContainerServiceList)) !== \count($testContainerServiceList)) {
            throw new DuplicateServiceInTestContainerException();
        }

        return $testContainerServiceList ?? [];
    }

    /**
     * @return string[]
     */
    private function getPublicServices(ContainerBuilder $containerBuilder): array
    {
        $services = [];
        foreach ($containerBuilder->getDefinitions() as $id => $definition) {
            if ($definition->isPublic() && !$definition->isAbstract()) {
                $services[] = $id;
            }
        }

        return $services;
    }
}
