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

namespace Acme\App\Test\TestCase\TestFramework\CompilerPass\CreateTestContainer;

use Acme\App\Test\Framework\AbstractUnitTest;
use Acme\App\Test\Framework\CompilerPass\CreateTestContainer\CreateTestContainerCompilerPass;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @small
 *
 * @internal
 */
final class CreateTestContainerCompilerPassUnitTest extends AbstractUnitTest
{
    /**
     * @var MockInterface|ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var CreateTestContainerCompilerPass
     */
    private $compilerPass;

    protected function setUp(): void
    {
        $this->containerBuilder = Mockery::mock(ContainerBuilder::class);
        $this->compilerPass = new CreateTestContainerCompilerPass();
    }

    /**
     * @test
     */
    public function process_adds_correct_services_to_test_container(): void
    {
        $this->mockTestContainerServiceListParameter($this->getTestContainerServiceList());
        $this->mockContainerDefinitionList();

        $this->expectTestContainerToBeCreated(
            [
                'public.instanceable' => new Reference('public.instanceable'),
                'private.instanceable' => new Reference('private.instanceable'),
                'private.instanceable.another' => new Reference('private.instanceable.another'),
            ]
        );

        $this->compilerPass->process($this->containerBuilder);
    }

    /**
     * @test
     */
    public function process_throws_exception_if_duplicated_service_in_test_container(): void
    {
        $this->expectException(\Acme\App\Test\Framework\CompilerPass\CreateTestContainer\DuplicateServiceInTestContainerException::class);

        $this->mockTestContainerServiceListParameter($this->getTestContainerServiceListWithDuplicatedService());

        $this->compilerPass->process($this->containerBuilder);
    }

    /**
     * @test
     */
    public function process_throws_exception_if_abstract_service_in_test_container(): void
    {
        $this->expectException(\Acme\App\Test\Framework\CompilerPass\CreateTestContainer\AbstractServiceInTestContainerException::class);

        $this->mockTestContainerServiceListParameter($this->getTestContainerServiceListWithAbstractService());
        $this->mockContainerDefinitionList();

        $this->expectTestContainerNotToBeCreated();

        $this->compilerPass->process($this->containerBuilder);
    }

    /**
     * @test
     */
    public function process_throws_exception_if_inexistent_service_in_test_container(): void
    {
        $this->expectException(\Acme\App\Test\Framework\CompilerPass\CreateTestContainer\ServiceInTestContainerNotFoundInProductionContainerException::class);

        $this->mockTestContainerServiceListParameter($this->getTestContainerServiceListWithInexistentService());
        $this->mockContainerDefinitionList();

        $this->expectTestContainerNotToBeCreated();

        $this->compilerPass->process($this->containerBuilder);
    }

    private function getTestContainerServiceList(): array
    {
        return ['private.instanceable', 'private.instanceable.another'];
    }

    private function getTestContainerServiceListWithDuplicatedService(): array
    {
        return [
            'private.instanceable',
            'private.instanceable',
        ];
    }

    private function getTestContainerServiceListWithAbstractService(): array
    {
        return [
            'private.instanceable',
            'public.abstract',
        ];
    }

    private function getTestContainerServiceListWithInexistentService(): array
    {
        return [
            'private.instanceable',
            'inexistent',
        ];
    }

    private function getDefinitionList(): array
    {
        return [
             'private.instanceable' => $this->getDefinition(false, false),
             'private.instanceable.another' => $this->getDefinition(false, false),
             'private.abstract' => $this->getDefinition(false, true),
             'public.abstract' => $this->getDefinition(true, true),
             'public.instanceable' => $this->getDefinition(true, false),
         ];
    }

    /**
     * @return MockInterface|Definition
     */
    private function getDefinition(bool $isPublic, bool $isAbstract): Definition
    {
        $definitionMock = Mockery::mock(Definition::class);
        $definitionMock->shouldReceive('isPublic')->andReturn($isPublic);
        $definitionMock->shouldReceive('isAbstract')->andReturn($isAbstract);

        return $definitionMock;
    }

    private function mockTestContainerServiceListParameter(array $testContainerServiceList): void
    {
        $this->containerBuilder->shouldReceive('hasParameter')
            ->once()
            ->with(CreateTestContainerCompilerPass::TEST_CONTAINER_SERVICE_LIST)
            ->andReturn(true);

        $this->containerBuilder->shouldReceive('getParameter')
            ->once()
            ->with(CreateTestContainerCompilerPass::TEST_CONTAINER_SERVICE_LIST)
            ->andReturn($testContainerServiceList);
    }

    private function mockContainerDefinitionList(): void
    {
        $this->containerBuilder->shouldReceive('getDefinitions')
            ->with()
            ->andReturn($this->getDefinitionList());

        $this->containerBuilder->shouldReceive('findDefinition')
            ->with(Mockery::type('string'))
            ->andReturnUsing(
                function ($id) {
                    return $this->getDefinitionList()[$id];
                }
            );

        $this->containerBuilder->shouldReceive('has')
            ->with(Mockery::type('string'))
            ->andReturnUsing(
                function ($id) {
                    return (bool) ($this->getDefinitionList()[$id] ?? false);
                }
            );
    }

    private function expectTestContainerToBeCreated(...$expectedArguments): void
    {
        $definitionMock = Mockery::mock(Definition::class);
        $definitionMock->shouldReceive('setPublic')->once()->with(true)->andReturn($definitionMock);
        $definitionMock->shouldReceive('addTag')
            ->once()
            ->with(CreateTestContainerCompilerPass::CONTAINER_SERVICE_LOCATOR)
            ->andReturn($definitionMock);

        $definitionMock->shouldReceive('setArguments')
            ->once()
            ->with($expectedArguments)
            ->andReturn($definitionMock);

        $this->containerBuilder->shouldReceive('register')
            ->once()
            ->with(CreateTestContainerCompilerPass::TEST_CONTAINER, ServiceLocator::class)
            ->andReturn($definitionMock);
    }

    private function expectTestContainerNotToBeCreated(): void
    {
        $definitionMock = Mockery::mock(Definition::class);
        $definitionMock->shouldReceive('setPublic')->once()->with(true)->andReturn($definitionMock);
        $definitionMock->shouldReceive('addTag')
            ->once()
            ->with(CreateTestContainerCompilerPass::CONTAINER_SERVICE_LOCATOR)
            ->andReturn($definitionMock);

        $definitionMock->shouldNotReceive('setArguments');

        $this->containerBuilder->shouldReceive('register')
            ->once()
            ->with(CreateTestContainerCompilerPass::TEST_CONTAINER, ServiceLocator::class)
            ->andReturn($definitionMock);
    }
}
