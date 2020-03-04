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

namespace Acme\App\Test\TestCase\Presentation\Web\Infrastructure\FlashMessage\Symfony;

use Acme\App\Presentation\Web\Core\Port\FlashMessage\FlashMessageServiceInterface;
use Acme\App\Presentation\Web\Infrastructure\FlashMessage\Symfony\FlashMessageService;
use Acme\App\Test\Framework\AbstractIntegrationTest;
use Acme\PhpExtension\Helper\StringHelper;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @medium
 *
 * @internal
 */
final class FlashMessageServiceIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @test
     * @dataProvider provideMethodNameList
     */
    public function messages_go_into_the_flash_message_bag(string $methodName): void
    {
        $message = 'a message';
        $this->getFlashMessageService()->$methodName($message);

        self::assertContains($message, $this->getSessionFlashBag()->get($methodName));
    }

    /**
     * @throws \ReflectionException
     */
    public function provideMethodNameList(): array
    {
        $constantList = (new ReflectionClass(FlashMessageServiceInterface::class))->getConstants();

        $methodNameList = array_map(
            function ($constant) {
                return [StringHelper::toCamelCase($constant)];
            },
            $constantList
        );

        return $methodNameList;
    }

    protected function getSessionFlashBag(): FlashBagInterface
    {
        /** @var Session $session */
        $session = $this->getService('session');

        return $session->getFlashBag();
    }

    protected function getFlashMessageService(): FlashMessageService
    {
        return $this->getService(FlashMessageServiceInterface::class);
    }
}
