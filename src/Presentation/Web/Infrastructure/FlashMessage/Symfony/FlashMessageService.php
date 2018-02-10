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

namespace Acme\App\Presentation\Web\Infrastructure\FlashMessage\Symfony;

use Acme\App\Presentation\Web\Core\Port\FlashMessage\FlashMessageServiceInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final class FlashMessageService implements FlashMessageServiceInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function success(string $message): void
    {
        $this->add(self::SUCCESS, $message);
    }

    public function info(string $message): void
    {
        $this->add(self::INFO, $message);
    }

    public function warning(string $message): void
    {
        $this->add(self::WARNING, $message);
    }

    public function danger(string $message): void
    {
        $this->add(self::DANGER, $message);
    }

    private function add(string $type, string $message): void
    {
        $this->flashBag->add($type, $message);
    }
}
