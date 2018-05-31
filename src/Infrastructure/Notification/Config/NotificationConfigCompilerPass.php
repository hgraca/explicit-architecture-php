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

namespace Acme\App\Infrastructure\Notification\Config;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class NotificationConfigCompilerPass implements CompilerPassInterface
{
    public const KEY_NOTIFICATION = 'notification';
    public const KEY_GENERATOR_METHOD_NAME = 'method';
    public const KEY_VOTER = 'voterId';
    public const KEY_VOTER_METHOD_NAME = 'voterMethod';
    public const NOTIFICATION_STRATEGY_TAG = 'notification_strategy';

    public function process(ContainerBuilder $containerBuilder): void
    {
        $notificationStrategyList = $containerBuilder->findTaggedServiceIds(self::NOTIFICATION_STRATEGY_TAG);

        foreach ($notificationStrategyList as $serviceId => $tags) {
            $this->processNotificationStrategy($containerBuilder, $serviceId);
        }
    }

    private function processNotificationStrategy(ContainerBuilder $containerBuilder, string $strategyServiceId): void
    {
        if (!$containerBuilder->has($strategyServiceId)) {
            return;
        }

        $definition = $containerBuilder->findDefinition($strategyServiceId);

        $taggedServices = $containerBuilder->findTaggedServiceIds($strategyServiceId);

        foreach ($taggedServices as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                if (!$containerBuilder->has($serviceId)) {
                    throw new NotificationConfigException(
                        "The configured notification generator '$serviceId' does not exist in the container"
                    );
                }

                $methodName = $attributes[self:: KEY_GENERATOR_METHOD_NAME];
                if (!method_exists($containerBuilder->getDefinition($serviceId)->getClass(), $methodName)) {
                    throw new NotificationConfigException(
                        "The configured notification generator '$serviceId'"
                        . " does not have the configured method '$methodName'"
                    );
                }

                $definition->addMethodCall(
                    'addNotificationMessageGenerator',
                    [
                        new Reference($serviceId),
                        $attributes[self::KEY_NOTIFICATION],
                        $methodName,
                        array_key_exists(self::KEY_VOTER, $attributes)
                            ? new Reference($attributes[self::KEY_VOTER])
                            : null,
                        $attributes[self::KEY_VOTER_METHOD_NAME] ?? null,
                    ]
                );
            }
        }
    }
}
