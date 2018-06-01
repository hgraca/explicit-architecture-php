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

namespace Acme\App\Test\TestCase\Presentation\Web\Core\Component;

use Acme\App\Test\Framework\AbstractFunctionalTest;

/**
 * @large
 */
final class BackslashRedirectControllerFunctionalTest extends AbstractFunctionalTest
{
    /**
     * @test
     */
    public function requests_ending_on_a_backslash_redirect_to_same_url_without_backslash(): void
    {
        $expectedUrl = 'http://localhost/en/blog/posts';
        $client = static::createClient();
        $client->request('GET', $expectedUrl . '/');
        $response = $client->getResponse();
        self::assertSame(301, $response->getStatusCode());
        self::assertTrue($response->headers->has('location'));
        self::assertSame($expectedUrl, $response->headers->get('location'));
    }
}
