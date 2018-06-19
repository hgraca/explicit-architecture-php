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

namespace Acme\App\Test\TestCase\Infrastructure\EventDispatcher;

use Acme\App\Infrastructure\EventDispatcher\SyncEventDispatcher;
use Acme\App\Infrastructure\EventDispatcher\SyncEventDispatcherCompilerPass;
use Acme\App\Test\Framework\AbstractUnitTest;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @small
 */
final class SyncEventDispatcherCompilerPassUnitTest extends AbstractUnitTest
{
    private const DUMMY_LISTENER_CLASS_01 = OneDummyListener::class;
    private const DUMMY_EVENT_CLASS_01 = 'DUMMY_EVENT_CLASS_01';
    private const DUMMY_LISTENER_METHOD_01 = 'handle';

    private const DUMMY_LISTENER_CLASS_02 = TwoDummyListener::class;
    private const DUMMY_EVENT_CLASS_02 = 'DUMMY_EVENT_CLASS_02';
    private const DUMMY_LISTENER_METHOD_02 = 'handleOne';
    private const DUMMY_EVENT_CLASS_03 = 'DUMMY_EVENT_CLASS_03';
    private const DUMMY_LISTENER_METHOD_03 = 'handleAnother';

    private const TAGGED_SERVICES = [
        self::DUMMY_LISTENER_CLASS_01 => [
            [
                SyncEventDispatcherCompilerPass::EVENT => self::DUMMY_EVENT_CLASS_01,
                SyncEventDispatcherCompilerPass::METHOD => self::DUMMY_LISTENER_METHOD_01,
            ],
        ],
        self::DUMMY_LISTENER_CLASS_02 => [
            [
                SyncEventDispatcherCompilerPass::EVENT => self::DUMMY_EVENT_CLASS_02,
                SyncEventDispatcherCompilerPass::METHOD => self::DUMMY_LISTENER_METHOD_02,
            ],
            [
                SyncEventDispatcherCompilerPass::EVENT => self::DUMMY_EVENT_CLASS_03,
                SyncEventDispatcherCompilerPass::METHOD => self::DUMMY_LISTENER_METHOD_03,
            ],
        ],
    ];

    /**
     * @var MockInterface|ContainerBuilder
     */
    private $container;

    /**
     * @var SyncEventDispatcherCompilerPass
     */
    private $compilerPass;

    public function setUp(): void
    {
        $this->container = Mockery::mock(ContainerBuilder::class);
        $this->compilerPass = new SyncEventDispatcherCompilerPass();
    }

    /**
     * @test
     */
    public function process_adds_correct_method_calls_to_dispatcher_definition(): void
    {
        $definitionMock = Mockery::mock(Definition::class);
        /**
         * @var string
         * @var string[][] $tagList
         */
        foreach (self::TAGGED_SERVICES as $listenerId => $tagList) {
            $listenerDefinitionMock = Mockery::mock(Definition::class);
            $listenerDefinitionMock->shouldReceive('getClass')->andReturn($listenerId);
            $this->container->shouldReceive('getDefinition')
                ->withArgs([$listenerId])
                ->andReturn($listenerDefinitionMock);

            foreach ($tagList as $attributeList) {
                $definitionMock->shouldReceive('addMethodCall')
                    ->once()
                    ->with(
                        SyncEventDispatcherCompilerPass::METHOD_CALL,
                        [
                            $attributeList[SyncEventDispatcherCompilerPass::EVENT],
                            [
                                new Reference($listenerId),
                                $attributeList[SyncEventDispatcherCompilerPass::METHOD],
                            ],
                            $attributeList[SyncEventDispatcherCompilerPass::PRIORITY] ?? SyncEventDispatcherCompilerPass::PRIORITY_DEFAULT,
                        ]
                    );
            }
        }

        $this->container->shouldReceive('findDefinition')
            ->once()
            ->with(SyncEventDispatcher::class)
            ->andReturn($definitionMock);

        $this->container->shouldReceive('findTaggedServiceIds')->once()->andReturn(self::TAGGED_SERVICES);

        $this->compilerPass->process($this->container);
    }
}
