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

namespace Acme\App\Infrastructure\Framework\Symfony\CompilerPass;

use Acme\App\Infrastructure\EventDispatcher\SyncEventDispatcher;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class SyncEventDispatcherCompilerPass implements CompilerPassInterface
{
    public const SYNC_EVENT_DISPATCHER_TAG = 'sync_event_dispatcher';
    public const TAG_NAME_KEY = 'name';
    public const EVENT = 'event';
    public const METHOD = 'method';
    public const PRIORITY = 'priority';
    public const PRIORITY_DEFAULT = 0;
    public const METHOD_CALL = 'addDestination';

    public function process(ContainerBuilder $containerBuilder): void
    {
        $eventDispatcherDefinition = $containerBuilder->findDefinition(SyncEventDispatcher::class);

        foreach ($containerBuilder->findTaggedServiceIds(self::SYNC_EVENT_DISPATCHER_TAG) as $listenerId => $tagList) {
            $listenerDefinition = $containerBuilder->getDefinition($listenerId);
            /** @var array $tagList */
            foreach ($tagList as $attributeList) {
                if (!method_exists($listenerDefinition->getClass(), $attributeList[self::METHOD])) {
                    throw new InvalidArgumentException(
                        'The configured listener ' . $listenerId
                        . ' does not have the configured method ' . $attributeList[self::METHOD]
                    );
                }

                $eventDispatcherDefinition->addMethodCall(
                    self::METHOD_CALL,
                    [
                        $attributeList[self::EVENT],
                        [new Reference($listenerId), $attributeList[self::METHOD]],
                        $attributeList[self::PRIORITY] ?? self::PRIORITY_DEFAULT,
                    ]
                );
            }
        }
    }
}
