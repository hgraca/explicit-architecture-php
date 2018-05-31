<?php

declare(strict_types=1);

return [
    Acme\App\Test\Framework\CompilerPass\CreateTestContainer\CreateTestContainerCompilerPass::class => ['test' => true],
    Acme\App\Infrastructure\Framework\Symfony\CompilerPass\SyncEventDispatcherCompilerPass::class => ['all' => true],
    Acme\App\Infrastructure\Notification\Config\NotificationConfigCompilerPass::class => ['all' => true],
];
