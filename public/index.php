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

use Acme\App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/../vendor/autoload.php';

// The check is to ensure we don't use .env in production
if (!isset($_ENV['APP_ENV'])) {
    (new Dotenv())->load(__DIR__ . '/../.env');
}

if ($_ENV['APP_DEBUG'] ?? ('prod' !== ($_ENV['APP_ENV'] ?? 'dev'))) {
    umask(0000);

    Debug::enable();
}

// Request::setTrustedProxies(['0.0.0.0/0'], Request::HEADER_FORWARDED);

$kernel = new Kernel(
    $_ENV['APP_ENV'] ?? 'dev',
    (bool) ($_ENV['APP_DEBUG'] ?? ('prod' !== ($_ENV['APP_ENV'] ?? 'dev')))
);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
