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

namespace Acme\App\Test\TestCase\Core\Component\Blog\Application\Notification\NewComment\Email;

use Acme\App\Core\Component\Blog\Application\Notification\NewComment\Email\NewCommentEmailViewModel;
use Acme\App\Core\Port\TemplateEngine\TemplateEngineInterface;
use Acme\App\Test\Framework\AbstractIntegrationTest;

final class NewCommentEmailHtmlTemplateIntegrationTest extends AbstractIntegrationTest
{
    public const TEMPLATE = '@Core/Component/Blog/Application/Notification/NewComment/Email/NewCommentEmail.html.twig';

    /**
     * @var TemplateEngineInterface
     */
    private $templateEngine;

    protected function setUp(): void
    {
        $this->templateEngine = self::getService(TemplateEngineInterface::class);
    }

    /**
     * @test
     */
    public function generate(): void
    {
        $postTitle = 'some title';
        $linkToPost = 'some-link';
        $subject = 'some email subject';

        $renderedTemplate = $this->templateEngine->render(
            self::TEMPLATE,
            new NewCommentEmailViewModel($subject, $postTitle, $linkToPost)
        );

        self::assertValidHtml($renderedTemplate);
        self::assertContains($postTitle, $renderedTemplate);
        self::assertContains($linkToPost, $renderedTemplate);
    }
}
