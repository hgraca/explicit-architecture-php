<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function isContainerRunning(string $containerName): bool
{
    if (
        isContainerExists($containerName)
        && 'true' === trim(shell_exec("docker inspect -f '{{.State.Running}}' $containerName 2>/dev/null"))
    ) {
        return true;
    }

    return false;
}

function isContainerStopped(string $containerName): bool
{
    if (
        isContainerExists($containerName)
        && 'false' === trim(shell_exec("docker inspect -f '{{.State.Running}}' $containerName 2>/dev/null"))
    ) {
        return true;
    }

    return false;
}

function isContainerExists(string $containerName): bool
{
    if ('' === shell_exec("docker ps -a | grep $containerName")) {
        return false;
    }

    return true;
}
