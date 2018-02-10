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

namespace Acme\App\Presentation\Web\Core\Port\FlashMessage;

/**
 * Although it looks like this port/adapter is something that should be in the `App\Core\Port` and `App\Infrastructure`
 * respectively, the fact is that this service is not used in the core, it is only used in the web UI. If we would
 * remove the web UI we would not need this interface at all.
 *
 * So following Uncle Bob packaging principles, we keep together the code that works together.
 *
 * This application only uses the success type of flash messages, but the CSS is already prepared to use these four,
 * so its nice to have them here to be easily used when needed as the application grows.
 * If at some point the application needs more, then and only then, we add here the new flash message name as a method,
 * as a constant, and the needed CSS to the CSS files.
 *
 * @see https://getbootstrap.com/docs/3.3/components/#alerts
 */
interface FlashMessageServiceInterface
{
    public const SUCCESS = 'success';
    public const INFO = 'info';
    public const WARNING = 'warning';
    public const DANGER = 'danger';

    public function success(string $message): void;

    public function info(string $message): void;

    public function warning(string $message): void;

    public function danger(string $message): void;
}
