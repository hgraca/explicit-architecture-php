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

function isContainerRunning(string $containerName): bool
{
    if (
        isContainerExists($containerName)
        && trim(shell_exec("docker inspect -f '{{.State.Running}}' $containerName 2>/dev/null")) === 'true'
    ) {
        return true;
    }

    return false;
}

function isContainerStopped(string $containerName): bool
{
    if (
        isContainerExists($containerName)
        && trim(shell_exec("docker inspect -f '{{.State.Running}}' $containerName 2>/dev/null")) === 'false'
    ) {
        return true;
    }

    return false;
}

function isContainerExists(string $containerName): bool
{
    if (shell_exec("docker ps -a | grep $containerName") === '') {
        return false;
    }

    return true;
}

function assertDockerComposeVersion(): void
{
    $minimumVersion = '1.22';
    $currentVersion = shell_exec("docker-compose version --short");
    if ($currentVersion < $minimumVersion) {
        echo "The minimum docker-compose version is $minimumVersion \n";
        exit(1);
    }
}
